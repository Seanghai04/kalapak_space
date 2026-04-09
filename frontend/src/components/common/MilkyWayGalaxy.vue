<template>
  <div ref="container" class="milky-way-container" @mousemove="onMouseMove" @mouseleave="onMouseLeave">
    <canvas ref="canvas" class="milky-way-canvas" />
    <!-- Glow overlay layers -->
    <div class="galaxy-glow-core" />
    <div class="galaxy-glow-ring galaxy-glow-ring--1" />
    <div class="galaxy-glow-ring galaxy-glow-ring--2" />
    <div class="galaxy-glow-ring galaxy-glow-ring--3" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, reactive } from 'vue'

const canvas = ref(null)
const container = ref(null)
let animationId = null
let time = 0

const mouse = reactive({ x: 0.5, y: 0.5, active: false })

function onMouseMove(e) {
  const rect = container.value.getBoundingClientRect()
  mouse.x = (e.clientX - rect.left) / rect.width
  mouse.y = (e.clientY - rect.top) / rect.height
  mouse.active = true
}

function onMouseLeave() {
  mouse.active = false
}

onMounted(() => {
  const ctx = canvas.value.getContext('2d')
  const dpr = Math.min(window.devicePixelRatio || 1, 2)
  let width, height, cx, cy

  function resize() {
    const rect = container.value.getBoundingClientRect()
    width = rect.width
    height = rect.height
    canvas.value.width = width * dpr
    canvas.value.height = height * dpr
    canvas.value.style.width = width + 'px'
    canvas.value.style.height = height + 'px'
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0)
    cx = width / 2
    cy = height / 2
  }

  resize()

  // ── Galaxy star particles ──
  const STAR_COUNT = 2800
  const SPIRAL_ARMS = 4
  const ARM_SPREAD = 0.6
  const MAX_RADIUS = Math.min(width, height) * 0.44

  function createStar(i) {
    const arm = i % SPIRAL_ARMS
    const armAngle = (arm / SPIRAL_ARMS) * Math.PI * 2
    const t = Math.pow(Math.random(), 0.6) // bias toward center
    const radius = t * MAX_RADIUS
    const spiralAngle = armAngle + t * 3.2 + (Math.random() - 0.5) * ARM_SPREAD * (1 + t * 0.5)
    // Color distribution
    const colorRand = Math.random()
    let r, g, b
    if (colorRand < 0.35) {
      // Violet-purple stars
      r = 120 + Math.random() * 80
      g = 30 + Math.random() * 50
      b = 200 + Math.random() * 55
    } else if (colorRand < 0.65) {
      // Cyan-blue stars
      r = 0 + Math.random() * 60
      g = 160 + Math.random() * 92
      b = 220 + Math.random() * 35
    } else if (colorRand < 0.82) {
      // White-hot stars
      r = 200 + Math.random() * 55
      g = 200 + Math.random() * 55
      b = 220 + Math.random() * 35
    } else {
      // Warm pinkish
      r = 200 + Math.random() * 55
      g = 100 + Math.random() * 60
      b = 180 + Math.random() * 75
    }
    return {
      angle: spiralAngle,
      radius,
      size: (1 - t * 0.5) * (0.4 + Math.random() * 1.6),
      speed: 0.0003 + (1 - t) * 0.0012 + Math.random() * 0.0003,
      twinklePhase: Math.random() * Math.PI * 2,
      twinkleSpeed: 0.01 + Math.random() * 0.03,
      r, g, b,
      brightness: 0.4 + Math.random() * 0.6,
    }
  }

  const stars = Array.from({ length: STAR_COUNT }, (_, i) => createStar(i))

  // ── Nebula clouds (rendered as soft radial blobs) ──
  const NEBULA_COUNT = 18
  const nebulae = Array.from({ length: NEBULA_COUNT }, () => {
    const arm = Math.floor(Math.random() * SPIRAL_ARMS)
    const armAngle = (arm / SPIRAL_ARMS) * Math.PI * 2
    const t = 0.15 + Math.random() * 0.7
    const radius = t * MAX_RADIUS
    const angle = armAngle + t * 3.2 + (Math.random() - 0.5) * 0.4
    const isViolet = Math.random() > 0.5
    return {
      angle,
      radius,
      size: 30 + Math.random() * 80,
      speed: 0.0002 + (1 - t) * 0.0005,
      r: isViolet ? 123 : 0,
      g: isViolet ? 47 : 212,
      b: 255,
      alpha: 0.015 + Math.random() * 0.025,
      pulsePhase: Math.random() * Math.PI * 2,
      pulseSpeed: 0.005 + Math.random() * 0.01,
    }
  })

  // ── Shooting stars / meteors ──
  const METEOR_MAX = 3
  let meteors = []

  function spawnMeteor() {
    const angle = Math.random() * Math.PI * 2
    const startR = MAX_RADIUS * (0.5 + Math.random() * 0.5)
    return {
      x: cx + Math.cos(angle) * startR,
      y: cy + Math.sin(angle) * startR,
      vx: (Math.random() - 0.5) * 6 - Math.cos(angle) * 3,
      vy: (Math.random() - 0.5) * 6 - Math.sin(angle) * 3,
      life: 1,
      decay: 0.008 + Math.random() * 0.012,
      length: 20 + Math.random() * 40,
      width: 0.5 + Math.random() * 1.5,
      isCyan: Math.random() > 0.5,
    }
  }

  // ── Dust ring particles (orbital ring) ──
  const DUST_COUNT = 600
  const dustParticles = Array.from({ length: DUST_COUNT }, () => {
    const angle = Math.random() * Math.PI * 2
    const r = MAX_RADIUS * (0.2 + Math.random() * 0.85)
    return {
      angle,
      radius: r,
      size: 0.2 + Math.random() * 0.6,
      speed: 0.0001 + Math.random() * 0.0004,
      alpha: 0.1 + Math.random() * 0.2,
    }
  })

  // ── Pulse waves from center ──
  let pulseWaves = []
  let pulseTimer = 0

  // ── Draw function ──
  function draw() {
    time++
    ctx.clearRect(0, 0, width, height)

    // Mouse parallax offset
    const parallaxX = mouse.active ? (mouse.x - 0.5) * 20 : Math.sin(time * 0.001) * 3
    const parallaxY = mouse.active ? (mouse.y - 0.5) * 20 : Math.cos(time * 0.0013) * 3
    const galCx = cx + parallaxX
    const galCy = cy + parallaxY

    // ── Draw core glow (canvas layer) ──
    const coreGradient = ctx.createRadialGradient(galCx, galCy, 0, galCx, galCy, MAX_RADIUS * 0.35)
    coreGradient.addColorStop(0, `rgba(123, 47, 255, ${0.12 + Math.sin(time * 0.015) * 0.04})`)
    coreGradient.addColorStop(0.3, `rgba(0, 212, 255, ${0.05 + Math.sin(time * 0.02) * 0.02})`)
    coreGradient.addColorStop(1, 'rgba(0, 0, 0, 0)')
    ctx.fillStyle = coreGradient
    ctx.fillRect(0, 0, width, height)

    // ── Draw nebulae ──
    nebulae.forEach(n => {
      n.angle += n.speed
      const pulse = 1 + Math.sin(time * n.pulseSpeed + n.pulsePhase) * 0.2
      const x = galCx + Math.cos(n.angle) * n.radius
      const y = galCy + Math.sin(n.angle) * n.radius * 0.55 // elliptical tilt
      const grad = ctx.createRadialGradient(x, y, 0, x, y, n.size * pulse)
      grad.addColorStop(0, `rgba(${n.r}, ${n.g}, ${n.b}, ${n.alpha * pulse})`)
      grad.addColorStop(1, 'rgba(0, 0, 0, 0)')
      ctx.fillStyle = grad
      ctx.fillRect(x - n.size * pulse, y - n.size * pulse, n.size * pulse * 2, n.size * pulse * 2)
    })

    // ── Draw dust particles ──
    dustParticles.forEach(d => {
      d.angle += d.speed
      const x = galCx + Math.cos(d.angle) * d.radius
      const y = galCy + Math.sin(d.angle) * d.radius * 0.55
      ctx.beginPath()
      ctx.arc(x, y, d.size, 0, Math.PI * 2)
      ctx.fillStyle = `rgba(180, 160, 220, ${d.alpha * (0.5 + Math.sin(time * 0.01 + d.angle) * 0.5)})`
      ctx.fill()
    })

    // ── Draw stars ──
    stars.forEach(s => {
      s.angle += s.speed
      const twinkle = 0.5 + Math.sin(time * s.twinkleSpeed + s.twinklePhase) * 0.5
      const alpha = s.brightness * twinkle
      const x = galCx + Math.cos(s.angle) * s.radius
      const y = galCy + Math.sin(s.angle) * s.radius * 0.55 // tilt for perspective

      // Star glow
      if (s.size > 1) {
        ctx.beginPath()
        ctx.arc(x, y, s.size * 3, 0, Math.PI * 2)
        ctx.fillStyle = `rgba(${s.r}, ${s.g}, ${s.b}, ${alpha * 0.08})`
        ctx.fill()
      }

      // Star core
      ctx.beginPath()
      ctx.arc(x, y, s.size, 0, Math.PI * 2)
      ctx.fillStyle = `rgba(${s.r}, ${s.g}, ${s.b}, ${alpha})`
      ctx.fill()

      // Cross flare on bright stars
      if (s.size > 1.3 && twinkle > 0.7) {
        ctx.strokeStyle = `rgba(${s.r}, ${s.g}, ${s.b}, ${alpha * 0.3})`
        ctx.lineWidth = 0.3
        const flareLen = s.size * 4 * twinkle
        ctx.beginPath()
        ctx.moveTo(x - flareLen, y)
        ctx.lineTo(x + flareLen, y)
        ctx.moveTo(x, y - flareLen)
        ctx.lineTo(x, y + flareLen)
        ctx.stroke()
      }
    })

    // ── Pulse waves ──
    pulseTimer++
    if (pulseTimer > 180) {
      pulseTimer = 0
      pulseWaves.push({ radius: 0, alpha: 0.15, speed: 0.8 + Math.random() * 0.5 })
    }
    pulseWaves.forEach(pw => {
      pw.radius += pw.speed
      pw.alpha -= 0.0008
      if (pw.alpha > 0) {
        ctx.beginPath()
        ctx.ellipse(galCx, galCy, pw.radius, pw.radius * 0.55, 0, 0, Math.PI * 2)
        ctx.strokeStyle = `rgba(123, 47, 255, ${pw.alpha})`
        ctx.lineWidth = 1.5
        ctx.stroke()
        // Second ring
        ctx.beginPath()
        ctx.ellipse(galCx, galCy, pw.radius * 0.98, pw.radius * 0.55 * 0.98, 0, 0, Math.PI * 2)
        ctx.strokeStyle = `rgba(0, 212, 255, ${pw.alpha * 0.5})`
        ctx.lineWidth = 0.5
        ctx.stroke()
      }
    })
    pulseWaves = pulseWaves.filter(pw => pw.alpha > 0)

    // ── Shooting stars ──
    if (Math.random() < 0.008 && meteors.length < METEOR_MAX) {
      meteors.push(spawnMeteor())
    }
    meteors.forEach(m => {
      m.x += m.vx
      m.y += m.vy
      m.life -= m.decay

      if (m.life > 0) {
        const tailX = m.x - m.vx * m.length * m.life * 0.3
        const tailY = m.y - m.vy * m.length * m.life * 0.3
        const grad = ctx.createLinearGradient(tailX, tailY, m.x, m.y)
        const baseColor = m.isCyan ? '0, 212, 255' : '123, 47, 255'
        grad.addColorStop(0, `rgba(${baseColor}, 0)`)
        grad.addColorStop(1, `rgba(${baseColor}, ${m.life * 0.8})`)

        ctx.beginPath()
        ctx.moveTo(tailX, tailY)
        ctx.lineTo(m.x, m.y)
        ctx.strokeStyle = grad
        ctx.lineWidth = m.width * m.life
        ctx.lineCap = 'round'
        ctx.stroke()

        // Head glow
        ctx.beginPath()
        ctx.arc(m.x, m.y, 2 * m.life, 0, Math.PI * 2)
        ctx.fillStyle = `rgba(255, 255, 255, ${m.life * 0.6})`
        ctx.fill()
      }
    })
    meteors = meteors.filter(m => m.life > 0)

    // ── Bright center point ──
    const centerPulse = 0.8 + Math.sin(time * 0.02) * 0.2
    const centerGrad = ctx.createRadialGradient(galCx, galCy, 0, galCx, galCy, 8)
    centerGrad.addColorStop(0, `rgba(255, 255, 255, ${0.9 * centerPulse})`)
    centerGrad.addColorStop(0.3, `rgba(200, 180, 255, ${0.4 * centerPulse})`)
    centerGrad.addColorStop(1, 'rgba(123, 47, 255, 0)')
    ctx.beginPath()
    ctx.arc(galCx, galCy, 8, 0, Math.PI * 2)
    ctx.fillStyle = centerGrad
    ctx.fill()

    animationId = requestAnimationFrame(draw)
  }

  window.addEventListener('resize', resize)
  draw()

  onUnmounted(() => {
    cancelAnimationFrame(animationId)
    window.removeEventListener('resize', resize)
  })
})
</script>

<style scoped>
.milky-way-container {
  position: absolute;
  inset: 0;
  overflow: hidden;
  pointer-events: auto;
}

.milky-way-canvas {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
}

/* ── CSS glow overlays for extra depth ── */
.galaxy-glow-core {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 200px;
  height: 120px;
  transform: translate(-50%, -50%);
  background: radial-gradient(ellipse, rgba(123, 47, 255, 0.2) 0%, rgba(0, 212, 255, 0.05) 40%, transparent 70%);
  filter: blur(30px);
  animation: coreBreath 4s ease-in-out infinite;
  pointer-events: none;
}

.galaxy-glow-ring {
  position: absolute;
  top: 50%;
  left: 50%;
  border-radius: 50%;
  transform: translate(-50%, -50%) scaleY(0.55);
  pointer-events: none;
}

.galaxy-glow-ring--1 {
  width: 60%;
  height: 60%;
  border: 1px solid rgba(123, 47, 255, 0.06);
  box-shadow: 0 0 40px rgba(123, 47, 255, 0.04), inset 0 0 40px rgba(123, 47, 255, 0.02);
  animation: ringPulse 8s ease-in-out infinite;
}

.galaxy-glow-ring--2 {
  width: 40%;
  height: 40%;
  border: 1px solid rgba(0, 212, 255, 0.05);
  box-shadow: 0 0 30px rgba(0, 212, 255, 0.03), inset 0 0 30px rgba(0, 212, 255, 0.02);
  animation: ringPulse 6s ease-in-out 1s infinite;
}

.galaxy-glow-ring--3 {
  width: 80%;
  height: 80%;
  border: 1px solid rgba(123, 47, 255, 0.03);
  box-shadow: 0 0 60px rgba(123, 47, 255, 0.02);
  animation: ringPulse 10s ease-in-out 2s infinite;
}

@keyframes coreBreath {
  0%, 100% { opacity: 0.8; transform: translate(-50%, -50%) scale(1); }
  50% { opacity: 1; transform: translate(-50%, -50%) scale(1.3); }
}

@keyframes ringPulse {
  0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scaleY(0.55) scale(1); }
  50% { opacity: 1; transform: translate(-50%, -50%) scaleY(0.55) scale(1.05); }
}

@media (prefers-reduced-motion: reduce) {
  .galaxy-glow-core,
  .galaxy-glow-ring {
    animation: none;
  }
}
</style>
