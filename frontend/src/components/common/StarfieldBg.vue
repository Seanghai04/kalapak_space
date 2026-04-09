<template>
  <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
    <canvas ref="canvas" class="w-full h-full" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const canvas = ref(null)
let animationId = null

onMounted(() => {
  const ctx = canvas.value.getContext('2d')
  let width = (canvas.value.width = window.innerWidth)
  let height = (canvas.value.height = window.innerHeight)

  const stars = Array.from({ length: 120 }, () => ({
    x: Math.random() * width,
    y: Math.random() * height,
    r: Math.random() * 1.5 + 0.3,
    speed: Math.random() * 0.3 + 0.05,
    opacity: Math.random(),
    twinkleSpeed: Math.random() * 0.02 + 0.005,
  }))

  function draw() {
    ctx.clearRect(0, 0, width, height)
    stars.forEach((star) => {
      star.opacity += star.twinkleSpeed
      if (star.opacity > 1 || star.opacity < 0.1) star.twinkleSpeed *= -1

      ctx.beginPath()
      ctx.arc(star.x, star.y, star.r, 0, Math.PI * 2)
      ctx.fillStyle = `rgba(123, 47, 255, ${star.opacity * 0.6})`
      ctx.fill()

      // Glow
      ctx.beginPath()
      ctx.arc(star.x, star.y, star.r * 3, 0, Math.PI * 2)
      ctx.fillStyle = `rgba(0, 212, 255, ${star.opacity * 0.1})`
      ctx.fill()

      star.y -= star.speed
      if (star.y < -5) {
        star.y = height + 5
        star.x = Math.random() * width
      }
    })
    animationId = requestAnimationFrame(draw)
  }

  function handleResize() {
    width = canvas.value.width = window.innerWidth
    height = canvas.value.height = window.innerHeight
  }

  window.addEventListener('resize', handleResize)
  draw()

  onUnmounted(() => {
    cancelAnimationFrame(animationId)
    window.removeEventListener('resize', handleResize)
  })
})
</script>
