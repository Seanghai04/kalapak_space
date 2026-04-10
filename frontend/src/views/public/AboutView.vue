<template>
  <div class="about-page">

    <!-- ═══════════════════ HERO ═══════════════════ -->
    <section class="relative min-h-[55vh] sm:min-h-[70vh] flex items-center justify-center overflow-hidden">
      <!-- Animated grid background -->
      <div class="absolute inset-0 about-hero-grid opacity-[0.03] dark:opacity-[0.05]" />
      <!-- Gradient orbs -->
      <div class="absolute top-1/3 -left-20 sm:-left-40 w-[300px] sm:w-[500px] h-[300px] sm:h-[500px] rounded-full bg-brand-violet/15 blur-[80px] sm:blur-[120px] animate-float" />
      <div class="absolute bottom-1/3 -right-20 sm:-right-40 w-[250px] sm:w-[400px] h-[250px] sm:h-[400px] rounded-full bg-brand-cyan/15 blur-[70px] sm:blur-[100px] animate-float" style="animation-delay: 3s" />

      <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <!-- Badge -->
        <div data-aos="fade-down" data-aos-duration="800" class="inline-flex items-center gap-2 px-4 py-1.5 mb-6 rounded-full border border-brand-violet/20 dark:border-brand-cyan/20 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm">
          <span class="text-xs font-sans text-brand-violet dark:text-brand-cyan uppercase tracking-widest">// About us</span>
        </div>
        <h1 data-aos="fade-up" data-aos-duration="1000" class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-sans font-bold mb-4 sm:mb-6 leading-[1.1]">
          <span class="text-gray-900 dark:text-white">Passionate about</span><br />
          <span class="gradient-text">building the future</span>
        </h1>
        <p data-aos="fade-up" data-aos-delay="200" class="text-base sm:text-lg md:text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mb-3 sm:mb-4 font-sans leading-relaxed px-2 sm:px-0">
          We are a team of developers from Cambodia, united by a shared vision of using technology to create meaningful impact.
        </p>
        <p data-aos="fade-up" data-aos-delay="300" class="text-sm font-sans text-brand-violet/70 dark:text-brand-cyan/70 italic">
          "ជីវិតដែលរស់នៅដើម្បីអ្នកដទៃ ទើបជាជីវិតដែលមានតម្លៃ"
        </p>
      </div>
    </section>

    <!-- ═══════════════════ OUR STORY ═══════════════════ -->
    <section class="py-14 sm:py-18 lg:py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
          <!-- Left: text content -->
          <div data-aos="fade-right">
            <span class="inline-block text-xs font-sans text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// Our story</span>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-sans font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 leading-tight">
              From university to<br /><span class="gradient-text">real-world impact</span>
            </h2>
            <div class="space-y-4 text-gray-600 dark:text-gray-400 leading-relaxed">
              <p>
                Kalapak Code Team was founded by a group of Year-4 IT engineering students at the Royal University of Phnom Penh.
                United by the shared vision of using technology to create positive impact, we came together to build real-world projects,
                learn from each other, and contribute to the Cambodian tech ecosystem.
              </p>
              <p>
                Our name <strong class="text-gray-900 dark:text-white">"Kalapak" (កាលាប៉ាក់)</strong> reflects our mission: to package knowledge and deliver it through code.
                From web applications to mobile apps, we tackle challenges with a cosmic-level ambition.
              </p>
            </div>
            <!-- Mini stats -->
            <div class="grid grid-cols-3 gap-4 sm:gap-6 mt-8 sm:mt-10">
              <div v-for="(s, i) in miniStats" :key="i">
                <p class="text-xl sm:text-2xl md:text-3xl font-sans font-bold gradient-text">{{ s.value }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">{{ s.label }}</p>
              </div>
            </div>
          </div>
          <!-- Right: visual card -->
          <div data-aos="fade-left" class="relative">
            <div class="about-visual-card relative rounded-2xl sm:rounded-3xl overflow-hidden border border-gray-100 dark:border-dark-600 bg-white/80 dark:bg-dark-800/80 backdrop-blur-sm p-5 sm:p-8 md:p-10 shadow-glass dark:shadow-glass-dark">
              <!-- Decorative code lines -->
              <div class="font-sans text-xs sm:text-sm space-y-2 sm:space-y-3 text-gray-400 dark:text-gray-500 overflow-x-auto">
                <p><span class="text-brand-violet dark:text-brand-cyan">const</span> <span class="text-gray-700 dark:text-gray-300">team</span> = {</p>
                <p class="pl-6"><span class="text-brand-violet dark:text-brand-cyan">name</span>: <span class="text-green-600 dark:text-green-400">'Kalapak Code'</span>,</p>
                <p class="pl-6"><span class="text-brand-violet dark:text-brand-cyan">founded</span>: <span class="text-amber-600 dark:text-amber-400">2024</span>,</p>
                <p class="pl-6"><span class="text-brand-violet dark:text-brand-cyan">location</span>: <span class="text-green-600 dark:text-green-400">'Phnom Penh, Cambodia'</span>,</p>
                <p class="pl-6"><span class="text-brand-violet dark:text-brand-cyan">university</span>: <span class="text-green-600 dark:text-green-400">'RUPP'</span>,</p>
                <p class="pl-6"><span class="text-brand-violet dark:text-brand-cyan">mission</span>: <span class="text-green-600 dark:text-green-400">'Package knowledge, deliver through code'</span>,</p>
                <p class="pl-6"><span class="text-brand-violet dark:text-brand-cyan">stack</span>: [<span class="text-green-600 dark:text-green-400">'Vue'</span>, <span class="text-green-600 dark:text-green-400">'Laravel'</span>, <span class="text-green-600 dark:text-green-400">'Flutter'</span>, <span class="text-green-600 dark:text-green-400">'Docker'</span>],</p>
                <p class="pl-6"><span class="text-brand-violet dark:text-brand-cyan">passion</span>: <span class="text-amber-600 dark:text-amber-400">Infinity</span>,</p>
                <p>};</p>
                <p class="mt-4"><span class="text-brand-violet dark:text-brand-cyan">team</span>.<span class="text-yellow-600 dark:text-yellow-400">build</span>(<span class="text-green-600 dark:text-green-400">'the future'</span>); <span class="text-gray-400 dark:text-gray-600">// 🚀</span></p>
              </div>
              <!-- Glow accent -->
              <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-brand-violet/10 dark:bg-brand-cyan/10 rounded-full blur-3xl" />
            </div>
            <!-- Floating decorative dots -->
            <div class="absolute -top-4 -right-4 w-20 h-20 hidden sm:grid grid-cols-3 gap-2 opacity-20">
              <span v-for="d in 9" :key="d" class="w-2 h-2 rounded-full bg-brand-violet dark:bg-brand-cyan" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ MISSION & VISION ═══════════════════ -->
    <section class="py-14 sm:py-18 lg:py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-16" data-aos="fade-up">
          <span class="inline-block text-xs font-sans text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// Purpose</span>
          <h2 class="text-2xl sm:text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white leading-tight">
            What drives <span class="gradient-text">us forward</span>
          </h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6 sm:gap-8 max-w-5xl mx-auto">
          <!-- Mission -->
          <div data-aos="fade-up" class="group relative p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-500 hover:-translate-y-1 hover:shadow-xl dark:hover:shadow-glow/10">
            <div class="mission-icon-wrap relative w-14 h-14 rounded-2xl mb-6 flex items-center justify-center bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3">
              <svg class="w-7 h-7 mission-rocket" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /></svg>
              <span class="mission-glow absolute inset-0 rounded-2xl" />
            </div>
            <h3 class="text-xl font-sans font-bold text-gray-900 dark:text-white mb-3">Our Mission</h3>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
              To package knowledge and deliver it through code. We build practical, real-world applications that solve genuine problems while fostering growth within Cambodia's tech community.
            </p>
          </div>
          <!-- Vision -->
          <div data-aos="fade-up" data-aos-delay="100" class="group relative p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-500 hover:-translate-y-1 hover:shadow-xl dark:hover:shadow-glow/10">
            <div class="vision-icon-wrap relative w-14 h-14 rounded-2xl mb-6 flex items-center justify-center bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3">
              <svg class="w-7 h-7 vision-eye" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              <span class="vision-glow absolute inset-0 rounded-2xl" />
            </div>
            <h3 class="text-xl font-sans font-bold text-gray-900 dark:text-white mb-3">Our Vision</h3>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
              To become a leading student-driven tech team in Southeast Asia, inspiring the next generation of Cambodian developers and proving that world-class software can be built anywhere.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ TEAM MEMBERS ═══════════════════ -->
    <section class="py-14 sm:py-18 lg:py-24 relative overflow-hidden">
      <!-- Background orbit system -->
      <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <!-- Inner dashed ring with orbiting dots -->
        <div class="team-orbit-ring absolute w-[300px] sm:w-[500px] h-[300px] sm:h-[500px]">
          <svg class="w-full h-full" viewBox="0 0 500 500" fill="none">
            <circle cx="250" cy="250" r="248" stroke="url(#grad1)" stroke-width="1" stroke-dasharray="8 12" opacity="0.3" />
            <defs><linearGradient id="grad1" x1="0" y1="0" x2="500" y2="500"><stop offset="0%" stop-color="#7b2fff" /><stop offset="100%" stop-color="#00d4ff" /></linearGradient></defs>
          </svg>
          <span class="orbit-dot orbit-dot-1" />
          <span class="orbit-dot orbit-dot-2" />
        </div>
        <!-- Middle ring -->
        <div class="team-orbit-ring-reverse absolute w-[450px] sm:w-[750px] h-[450px] sm:h-[750px]">
          <svg class="w-full h-full" viewBox="0 0 750 750" fill="none">
            <circle cx="375" cy="375" r="373" stroke="url(#grad2)" stroke-width="1" stroke-dasharray="6 16" opacity="0.15" />
            <defs><linearGradient id="grad2" x1="0" y1="0" x2="750" y2="750"><stop offset="0%" stop-color="#00d4ff" /><stop offset="100%" stop-color="#7b2fff" /></linearGradient></defs>
          </svg>
          <span class="orbit-dot orbit-dot-3" />
        </div>
        <!-- Outer ring -->
        <div class="team-orbit-ring absolute w-[600px] sm:w-[1000px] h-[600px] sm:h-[1000px]" style="animation-duration:90s">
          <svg class="w-full h-full" viewBox="0 0 1000 1000" fill="none">
            <circle cx="500" cy="500" r="498" stroke="url(#grad3)" stroke-width="1" stroke-dasharray="4 20" opacity="0.08" />
            <defs><linearGradient id="grad3" x1="0" y1="0" x2="1000" y2="1000"><stop offset="0%" stop-color="#7b2fff" /><stop offset="100%" stop-color="#00d4ff" /></linearGradient></defs>
          </svg>
          <span class="orbit-dot orbit-dot-4" />
        </div>
        <!-- Center glow -->
        <div class="absolute w-40 h-40 rounded-full bg-brand-violet/5 blur-[80px]" />
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="text-center mb-10 sm:mb-16" data-aos="fade-up">
          <span class="inline-block text-xs font-sans text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// The team</span>
          <h2 class="text-2xl sm:text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white leading-tight">
            Meet the <span class="gradient-text">people</span>
          </h2>
          <p class="text-gray-500 dark:text-gray-400 mt-4 max-w-lg mx-auto text-sm leading-relaxed">
            Every great product is built by great people. Here's the crew behind Kalapak.
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
          <div v-for="(member, i) in teamMembers" :key="member.id || i" data-aos="fade-up" :data-aos-delay="i * 120"
            class="team-card group relative text-center"
            @mousemove="handleCardTilt($event, i)" @mouseleave="resetCardTilt(i)">
            <div :ref="el => { if (el) cardRefs[i] = el }"
              class="team-card-inner relative rounded-xl sm:rounded-[2rem] border border-gray-100/50 dark:border-dark-600/80 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm p-4 sm:p-6 lg:p-8 transition-all duration-500 hover:border-brand-cyan/40 hover:shadow-[0_0_50px_rgba(0,212,255,0.1)]">

              <!-- Animated ring behind avatar -->
              <div class="relative w-20 h-20 sm:w-28 sm:h-28 lg:w-36 lg:h-36 mx-auto mb-3 sm:mb-4 lg:mb-6">
                <!-- Outer orbit circle with traveling dots -->
                <div class="absolute inset-[-16px] rounded-full team-avatar-orbit opacity-40 group-hover:opacity-100 transition-opacity duration-700">
                  <svg class="w-full h-full" viewBox="0 0 200 200" fill="none">
                    <circle cx="100" cy="100" r="98" stroke="url(#avatarGrad)" stroke-width="0.5" stroke-dasharray="4 8" />
                    <defs><linearGradient id="avatarGrad"><stop offset="0%" stop-color="#7b2fff" /><stop offset="100%" stop-color="#00d4ff" /></linearGradient></defs>
                  </svg>
                </div>
                <!-- Double spinning gradient ring -->
                <div class="absolute inset-[-4px] rounded-full team-avatar-ring opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                <div class="absolute inset-[-7px] rounded-full team-avatar-ring-outer opacity-0 group-hover:opacity-60 transition-opacity duration-700" />
                <!-- Multi-layer glow -->
                <div class="absolute inset-[-12px] rounded-full bg-brand-cyan/10 blur-2xl opacity-0 group-hover:opacity-80 transition-opacity duration-700 team-glow-pulse" />
                <div class="absolute inset-[-20px] rounded-full bg-brand-violet/8 blur-3xl opacity-0 group-hover:opacity-60 transition-opacity duration-1000" />
                <!-- Avatar circle -->
                <div class="relative w-20 h-20 sm:w-28 sm:h-28 lg:w-36 lg:h-36 rounded-full overflow-hidden ring-[3px] ring-gray-200/40 dark:ring-dark-500/40 group-hover:ring-0 transition-all duration-500 z-10">
                  <img v-if="member.avatar" :src="member.avatar" :alt="member.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                  <div v-else class="w-full h-full bg-gradient-brand flex items-center justify-center text-white text-3xl font-sans font-bold">
                    {{ member.name.charAt(0) }}
                  </div>
                  <!-- Shine sweep on hover -->
                  <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-in-out" />
                </div>
                <!-- Multiple orbiting dots -->
                <div class="absolute inset-[-6px] rounded-full team-orbit-dot opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                  <span class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-2.5 h-2.5 rounded-full bg-brand-cyan shadow-[0_0_12px_rgba(0,212,255,0.9)]" />
                </div>
                <div class="absolute inset-[-6px] rounded-full team-orbit-dot-reverse opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                  <span class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 w-1.5 h-1.5 rounded-full bg-brand-violet shadow-[0_0_10px_rgba(123,47,255,0.9)]" />
                </div>
                <!-- Corner sparkles -->
                <span class="absolute -top-2 -right-2 w-1 h-1 rounded-full bg-brand-cyan opacity-0 group-hover:opacity-100 team-sparkle" style="animation-delay:0s" />
                <span class="absolute -bottom-1 -left-3 w-1.5 h-1.5 rounded-full bg-brand-violet opacity-0 group-hover:opacity-100 team-sparkle" style="animation-delay:0.4s" />
                <span class="absolute top-4 -left-4 w-1 h-1 rounded-full bg-brand-cyan opacity-0 group-hover:opacity-100 team-sparkle" style="animation-delay:0.8s" />
                <!-- Founder badge -->
                <div v-if="member.is_founder" class="absolute -bottom-2 left-1/2 -translate-x-1/2 z-20 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gradient-brand text-white shadow-glow whitespace-nowrap team-badge-float">
                  Founder
                </div>
              </div>

              <!-- Info -->
              <h3 class="text-sm sm:text-base lg:text-lg font-sans font-bold text-gray-900 dark:text-white mb-0.5 sm:mb-1 transition-all duration-500 group-hover:text-brand-cyan">{{ member.name }}</h3>
              <p class="text-xs sm:text-sm text-brand-violet dark:text-brand-cyan mb-2 sm:mb-3 font-semibold transition-all duration-300">{{ member.title || member.role }}</p>
              <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-3 sm:mb-5 line-clamp-2 sm:line-clamp-3 transition-all duration-500 group-hover:text-gray-400 dark:group-hover:text-gray-300">{{ member.bio || member.specialty }}</p>

              <!-- Social links -->
              <div class="flex items-center justify-center gap-2 sm:gap-3">
                <a v-if="member.github_url" :href="member.github_url" target="_blank" rel="noopener noreferrer"
                  class="team-social-btn w-9 h-9 rounded-full flex items-center justify-center text-gray-400 border border-gray-200/50 dark:border-dark-500/50 hover:text-white hover:bg-brand-violet hover:border-brand-violet hover:shadow-[0_0_15px_rgba(123,47,255,0.4)] transition-all duration-300 hover:scale-110"
                  :style="{ transitionDelay: '0.05s' }">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                </a>
                <a v-if="member.linkedin_url" :href="member.linkedin_url" target="_blank" rel="noopener noreferrer"
                  class="team-social-btn w-9 h-9 rounded-full flex items-center justify-center text-gray-400 border border-gray-200/50 dark:border-dark-500/50 hover:text-white hover:bg-brand-violet hover:border-brand-violet hover:shadow-[0_0_15px_rgba(123,47,255,0.4)] transition-all duration-300 hover:scale-110"
                  :style="{ transitionDelay: '0.1s' }">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
                <a v-if="member.telegram_url" :href="member.telegram_url" target="_blank" rel="noopener noreferrer"
                  class="team-social-btn w-9 h-9 rounded-full flex items-center justify-center text-gray-400 border border-gray-200/50 dark:border-dark-500/50 hover:text-white hover:bg-brand-cyan hover:border-brand-cyan hover:shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all duration-300 hover:scale-110"
                  :style="{ transitionDelay: '0.15s' }">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                </a>
                <a v-if="member.email" :href="'mailto:' + member.email"
                  class="team-social-btn w-9 h-9 rounded-full flex items-center justify-center text-gray-400 border border-gray-200/50 dark:border-dark-500/50 hover:text-white hover:bg-brand-cyan hover:border-brand-cyan hover:shadow-[0_0_15px_rgba(0,212,255,0.4)] transition-all duration-300 hover:scale-110"
                  :style="{ transitionDelay: '0.2s' }">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                </a>
              </div>

              <!-- Bottom gradient line -->
              <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[2px] w-0 group-hover:w-3/4 bg-gradient-to-r from-transparent via-brand-cyan to-transparent transition-all duration-700 rounded-full" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ VALUES ═══════════════════ -->
    <section class="py-14 sm:py-18 lg:py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-16" data-aos="fade-up">
          <div>
            <span class="inline-block text-xs font-sans text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// Core values</span>
            <h2 class="text-2xl sm:text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white leading-tight">
              What we <span class="gradient-text">believe in</span>
            </h2>
          </div>
          <p class="text-gray-500 dark:text-gray-400 max-w-md text-sm leading-relaxed">
            These principles guide every line of code we write and every decision we make.
          </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
          <div v-for="(value, i) in values" :key="i" data-aos="fade-up" :data-aos-delay="i * 80"
            class="group relative p-8 rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-500 hover:-translate-y-1 hover:shadow-xl dark:hover:shadow-glow/10">
            <div class="w-14 h-14 rounded-2xl mb-6 flex items-center justify-center transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3"
              :class="value.bgClass">
              <span v-html="value.icon" />
            </div>
            <h3 class="text-lg font-sans font-bold text-gray-900 dark:text-white mb-3 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">{{ value.title }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ value.description }}</p>
            <!-- Hover gradient underline -->
            <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-gradient-brand opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-b-2xl" />
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ TIMELINE ═══════════════════ -->
    <section class="py-14 sm:py-18 lg:py-24 relative overflow-hidden">
      <!-- Background animated particles -->
      <div class="absolute inset-0 pointer-events-none">
        <span v-for="p in 12" :key="p" class="tl-particle absolute rounded-full bg-brand-violet/20 dark:bg-brand-cyan/20"
          :style="{
            width: (4 + (p % 3) * 3) + 'px',
            height: (4 + (p % 3) * 3) + 'px',
            left: ((p * 8.3) % 100) + '%',
            top: ((p * 17.7 + 10) % 90) + '%',
            animationDelay: (p * 0.6) + 's',
            animationDuration: (3 + (p % 3)) + 's'
          }"
        />
      </div>

      <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10 sm:mb-16" data-aos="fade-up">
          <span class="inline-block text-xs font-sans text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// Our journey</span>
          <h2 class="text-2xl sm:text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white leading-tight">
            How we <span class="gradient-text">got here</span>
          </h2>
        </div>

        <div class="relative">
          <!-- Animated timeline line -->
          <div class="absolute left-6 md:left-1/2 md:-translate-x-px top-0 bottom-0 w-0.5 overflow-hidden">
            <div class="tl-line-fill w-full h-full bg-gradient-to-b from-brand-violet via-brand-cyan to-brand-violet/20" />
            <!-- Traveling light dot -->
            <div class="tl-traveling-dot absolute left-1/2 -translate-x-1/2 w-1.5 h-8 rounded-full bg-gradient-to-b from-transparent via-white to-transparent opacity-60" />
          </div>

          <div v-for="(item, i) in timeline" :key="i"
            class="tl-item relative mb-16 last:mb-0"
            :data-aos="i % 2 === 0 ? 'fade-right' : 'fade-left'"
            :data-aos-delay="i * 150"
            data-aos-duration="800"
          >
            <div class="flex items-start gap-6 md:gap-0" :class="i % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'">
              <!-- Animated dot with rings -->
              <div class="absolute left-6 md:left-1/2 -translate-x-1/2 z-10 mt-6">
                <div class="tl-dot-wrap relative flex items-center justify-center">
                  <!-- Outer ping ring -->
                  <span class="absolute w-8 h-8 rounded-full border-2 border-brand-violet/40 dark:border-brand-cyan/40 tl-ping" :style="{ animationDelay: (i * 0.8) + 's' }" />
                  <!-- Middle glow ring -->
                  <span class="absolute w-5 h-5 rounded-full bg-brand-violet/20 dark:bg-brand-cyan/20 tl-glow-ring" :style="{ animationDelay: (i * 0.5) + 's' }" />
                  <!-- Core dot -->
                  <span class="relative w-4 h-4 rounded-full bg-gradient-to-br from-brand-violet to-brand-cyan shadow-lg shadow-brand-violet/30 dark:shadow-brand-cyan/30 tl-core-dot" />
                  <!-- Inner sparkle -->
                  <span class="absolute w-1.5 h-1.5 rounded-full bg-white/80 top-1 left-1 tl-sparkle" />
                </div>
              </div>

              <!-- Spacer for mobile -->
              <div class="w-12 flex-shrink-0 md:hidden" />

              <!-- Card -->
              <div class="flex-1 md:w-[calc(50%-2rem)]" :class="i % 2 === 0 ? 'md:pr-12' : 'md:pl-12'">
                <div class="tl-card group relative p-6 rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/70 dark:bg-dark-800/70 backdrop-blur-sm transition-all duration-500 hover:-translate-y-1 hover:shadow-2xl hover:shadow-brand-violet/10 dark:hover:shadow-brand-cyan/10 hover:border-brand-violet/30 dark:hover:border-brand-cyan/30">
                  <!-- Connecting line from dot to card -->
                  <div class="hidden md:block absolute top-8 w-8 h-px"
                    :class="i % 2 === 0
                      ? 'right-0 translate-x-full bg-gradient-to-r from-brand-violet/40 to-transparent dark:from-brand-cyan/40'
                      : 'left-0 -translate-x-full bg-gradient-to-l from-brand-violet/40 to-transparent dark:from-brand-cyan/40'"
                  />
                  <!-- Corner accent -->
                  <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden rounded-tr-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                    <div class="absolute -top-8 -right-8 w-16 h-16 bg-gradient-to-br from-brand-violet/10 to-brand-cyan/10 rotate-45" />
                  </div>
                  <!-- Step number -->
                  <div class="absolute -top-3 -left-3 w-7 h-7 rounded-full bg-gradient-to-br from-brand-violet to-brand-cyan flex items-center justify-center text-white text-[10px] font-bold shadow-md tl-step-badge opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:scale-110">
                    {{ String(i + 1).padStart(2, '0') }}
                  </div>
                  <!-- Date badge -->
                  <span class="inline-flex items-center gap-1.5 text-xs font-sans font-bold text-brand-violet dark:text-brand-cyan mb-2 px-2 py-0.5 rounded-full bg-brand-violet/5 dark:bg-brand-cyan/5 border border-brand-violet/10 dark:border-brand-cyan/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-violet dark:bg-brand-cyan tl-date-dot" />
                    {{ item.date }}
                  </span>
                  <h3 class="text-base font-sans font-bold text-gray-900 dark:text-white mb-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors duration-300">{{ item.title }}</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ item.description }}</p>
                  <!-- Bottom progress bar on hover -->
                  <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-2xl overflow-hidden">
                    <div class="tl-progress-bar h-full bg-gradient-to-r from-brand-violet to-brand-cyan w-0 group-hover:w-full transition-all duration-700 ease-out" />
                  </div>
                </div>
              </div>

              <!-- Spacer for desktop alternation -->
              <div class="hidden md:block flex-1 md:w-[calc(50%-2rem)]" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ WHY CHOOSE US ═══════════════════ -->
    <section class="py-14 sm:py-18 lg:py-24 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10 sm:mb-16" data-aos="fade-up">
          <span class="inline-block text-xs font-sans text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// Why us</span>
          <h2 class="text-2xl sm:text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white leading-tight">
            What makes us <span class="gradient-text">different</span>
          </h2>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
          <div v-for="(item, i) in differentiators" :key="i" data-aos="fade-up" :data-aos-delay="i * 80"
            class="group text-center p-8 rounded-2xl border border-gray-100 dark:border-dark-600 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-500 hover:-translate-y-1 hover:shadow-xl dark:hover:shadow-glow/10">
            <div class="w-16 h-16 rounded-full mx-auto mb-5 flex items-center justify-center bg-brand-violet/10 dark:bg-brand-cyan/10 text-brand-violet dark:text-brand-cyan group-hover:scale-110 transition-transform duration-500">
              <span v-html="item.icon" />
            </div>
            <h3 class="text-base font-sans font-bold text-gray-900 dark:text-white mb-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">{{ item.title }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ item.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ CTA ═══════════════════ -->
    <section class="py-14 sm:py-18 lg:py-24 relative">
      <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div data-aos="fade-up" class="relative rounded-2xl sm:rounded-3xl overflow-hidden">
          <!-- Background -->
          <div class="absolute inset-0 bg-gradient-brand opacity-90" />
          <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.1) 1px, transparent 0); background-size: 24px 24px;" />
          <!-- Content -->
          <div class="relative z-10 px-6 py-12 sm:px-8 sm:py-14 md:px-16 md:py-16 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-sans font-bold text-white mb-4">
              Want to work with us?
            </h2>
            <p class="text-sm sm:text-base text-white/80 max-w-xl mx-auto mb-6 sm:mb-8 leading-relaxed">
              Whether you have a project idea, want to collaborate, or just want to say hello — we'd love to hear from you.
            </p>
            <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-3 sm:gap-4 px-2 sm:px-0">
              <router-link to="/contact" class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3.5 sm:py-4 bg-white text-brand-violet font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300 hover:-translate-y-0.5 shadow-lg text-sm sm:text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                <span>Get in Touch</span>
              </router-link>
              <router-link to="/join" class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3.5 sm:py-4 border-2 border-white/30 text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300 hover:-translate-y-0.5 text-sm sm:text-base">
                <span>Join the Team</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { publicApi } from '@/services/api'

const teamMembers = ref([
  { name: 'Khat Vanna', role: 'Founder & Lead', specialty: 'Full-Stack Development', is_founder: true },
  { name: 'Rom Chamraeun', role: 'Co-Founder', specialty: 'Backend Engineering', is_founder: true },
  { name: 'Phuem Norng', role: 'Co-Founder', specialty: 'Frontend Development', is_founder: true },
  { name: 'Pheun Seanghai', role: 'Co-Founder', specialty: 'Mobile Development', is_founder: true },
])

const cardRefs = reactive({})

function handleCardTilt(e, i) {
  const el = cardRefs[i]
  if (!el) return
  const rect = el.getBoundingClientRect()
  const x = e.clientX - rect.left
  const y = e.clientY - rect.top
  const cx = rect.width / 2
  const cy = rect.height / 2
  const rotateX = ((y - cy) / cy) * -8
  const rotateY = ((x - cx) / cx) * 8
  el.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px)`
}

function resetCardTilt(i) {
  const el = cardRefs[i]
  if (!el) return
  el.style.transform = ''
}

onMounted(async () => {
  try {
    const { data } = await publicApi.getTeam()
    if (data.data && data.data.length) teamMembers.value = data.data
  } catch {}
})

const miniStats = [
  { value: '4+', label: 'Team Members' },
  { value: '2024', label: 'Founded' },
  { value: 'RUPP', label: 'University' },
]

const values = [
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg>',
    bgClass: 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400',
    title: 'Innovation First',
    description: 'We embrace new technologies and creative approaches, always pushing boundaries to solve problems in unique ways.'
  },
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" /></svg>',
    bgClass: 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400',
    title: 'Quality Code',
    description: 'Clean, maintainable, and well-documented code is at the heart of everything we build. We follow best practices always.'
  },
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>',
    bgClass: 'bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400',
    title: 'Team Collaboration',
    description: 'We believe in the power of teamwork. Every member brings unique skills that combine to create something greater.'
  },
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 003 12c0-1.605.42-3.113 1.157-4.418" /></svg>',
    bgClass: 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400',
    title: 'Community Impact',
    description: 'Technology should serve the community. We contribute to Cambodia\'s digital growth and inspire future developers.'
  },
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>',
    bgClass: 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400',
    title: 'Continuous Learning',
    description: 'As students, learning is in our DNA. We constantly explore new technologies, share knowledge, and grow together.'
  },
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>',
    bgClass: 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400',
    title: 'Open Source',
    description: 'We believe in giving back. Open source is part of our culture — sharing code, tools, and knowledge with the world.'
  },
]

const timeline = [
  { date: 'Early 2024', title: 'The Idea', description: 'Four IT engineering students at RUPP started discussing forming a team to build real-world projects together.' },
  { date: 'Mid 2024', title: 'Team Formation', description: 'Kalapak Code Team was officially formed. We chose our name to represent packaging knowledge through code.' },
  { date: 'Late 2024', title: 'First Projects', description: 'We began building our first collaborative projects, learning modern tech stacks and agile workflows.' },
  { date: '2025', title: 'Growing & Shipping', description: 'Launched our team website, expanded our portfolio, and started contributing to the Cambodian tech community.' },
  { date: 'The Future', title: 'Scaling Up', description: 'Aiming to take on larger projects, welcome new members, and make an even bigger impact in the tech scene.' },
]

const differentiators = [
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>',
    title: 'Student-Driven',
    description: 'Fresh perspectives and cutting-edge knowledge straight from university.'
  },
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L12 12.75 6.429 9.75m11.142 0l4.179 2.25L12 17.25 2.25 12l4.179-2.25m11.142 0l4.179 2.25-4.179 2.25M6.429 14.25L2.25 16.5 12 21.75l9.75-5.25-4.179-2.25" /></svg>',
    title: 'Full-Stack',
    description: 'End-to-end development from database design to pixel-perfect UIs.'
  },
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>',
    title: 'Modern Stack',
    description: 'Vue, Laravel, Flutter, Docker — always using the latest and best tools.'
  },
  {
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>',
    title: '100% Passion',
    description: 'We code because we love it. Every project is a labor of love and learning.'
  },
]
</script>

<style scoped>
.about-hero-grid {
  background-image:
    linear-gradient(rgba(123, 47, 255, 0.1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(123, 47, 255, 0.1) 1px, transparent 1px);
  background-size: 60px 60px;
}

/* ── Team Card Animations ── */
.team-card {
  perspective: 800px;
}
.team-card-inner {
  transform-style: preserve-3d;
  transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), border-color 0.5s, box-shadow 0.5s;
  will-change: transform;
}

/* Spinning gradient ring around avatar on hover */
.team-avatar-ring {
  background: conic-gradient(
    from 0deg,
    #7b2fff,
    #00d4ff,
    transparent,
    #7b2fff,
    #00d4ff,
    transparent,
    #7b2fff
  );
  animation: spinRing 2.5s linear infinite;
  -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 3px));
  mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 3px));
}

/* Outer thinner ring */
.team-avatar-ring-outer {
  background: conic-gradient(
    from 180deg,
    transparent,
    #00d4ff,
    transparent,
    #7b2fff,
    transparent
  );
  animation: spinRingReverse 4s linear infinite;
  -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 1.5px), #000 calc(100% - 1.5px));
  mask: radial-gradient(farthest-side, transparent calc(100% - 1.5px), #000 calc(100% - 1.5px));
}

/* Orbiting dots around avatar */
.team-orbit-dot {
  animation: spinRing 2s linear infinite;
}
.team-orbit-dot-reverse {
  animation: spinRingReverse 3s linear infinite;
}

/* Glow pulse */
.team-glow-pulse {
  animation: glowPulse 2s ease-in-out infinite;
}

/* Background orbit rings */
.team-orbit-ring {
  animation: spinRing 50s linear infinite;
}
.team-orbit-ring-reverse {
  animation: spinRingReverse 70s linear infinite;
}

/* Orbiting dots on background rings */
.orbit-dot {
  position: absolute;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #00d4ff;
  box-shadow: 0 0 12px 3px rgba(0, 212, 255, 0.6);
}
.orbit-dot-1 {
  top: 0;
  left: 50%;
  transform: translate(-50%, -50%);
  animation: orbitDotPulse 2s ease-in-out infinite;
}
.orbit-dot-2 {
  bottom: 0;
  left: 50%;
  transform: translate(-50%, 50%);
  background: #7b2fff;
  box-shadow: 0 0 12px 3px rgba(123, 47, 255, 0.6);
  animation: orbitDotPulse 2s ease-in-out infinite 1s;
  width: 4px;
  height: 4px;
}
.orbit-dot-3 {
  top: 50%;
  right: 0;
  transform: translate(50%, -50%);
  background: #7b2fff;
  box-shadow: 0 0 10px 2px rgba(123, 47, 255, 0.5);
  animation: orbitDotPulse 3s ease-in-out infinite 0.5s;
  width: 5px;
  height: 5px;
}
.orbit-dot-4 {
  top: 50%;
  left: 0;
  transform: translate(-50%, -50%);
  animation: orbitDotPulse 2.5s ease-in-out infinite 0.3s;
  width: 4px;
  height: 4px;
}

/* Sparkle effect */
.team-sparkle {
  animation: sparkle 1.5s ease-in-out infinite;
}

/* Founder badge float */
.team-badge-float {
  animation: badgeFloat 3s ease-in-out infinite;
}

/* Social buttons pop up on card hover */
.team-card:hover .team-social-btn {
  animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}

@keyframes spinRing {
  to { transform: rotate(360deg); }
}
@keyframes spinRingReverse {
  to { transform: rotate(-360deg); }
}
@keyframes glowPulse {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.08); }
}
@keyframes orbitDotPulse {
  0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
  50% { opacity: 1; transform: translate(-50%, -50%) scale(1.6); }
}
@keyframes sparkle {
  0%, 100% { opacity: 0; transform: scale(0); }
  50% { opacity: 1; transform: scale(1); }
}
@keyframes badgeFloat {
  0%, 100% { transform: translateX(-50%) translateY(0); }
  50% { transform: translateX(-50%) translateY(-3px); }
}
@keyframes popIn {
  0% { opacity: 0; transform: scale(0.5) translateY(8px); }
  100% { opacity: 1; transform: scale(1) translateY(0); }
}

@media (prefers-reduced-motion: reduce) {
  .team-avatar-ring,
  .team-avatar-ring-outer,
  .team-orbit-dot,
  .team-orbit-dot-reverse,
  .team-orbit-ring,
  .team-orbit-ring-reverse,
  .team-glow-pulse,
  .team-sparkle,
  .team-badge-float,
  .mission-rocket,
  .vision-eye,
  .mission-glow,
  .vision-glow,
  .tl-particle,
  .tl-line-fill,
  .tl-traveling-dot,
  .tl-ping,
  .tl-glow-ring,
  .tl-core-dot,
  .tl-sparkle,
  .tl-date-dot {
    animation: none;
  }
}

/* ── Mission & Vision Icon Animations ── */
.mission-rocket {
  animation: rocketFloat 3s ease-in-out infinite;
}
.vision-eye {
  animation: eyePulse 3.5s ease-in-out infinite;
}
.mission-glow {
  background: radial-gradient(circle, rgba(123, 47, 255, 0.15) 0%, transparent 70%);
  animation: iconGlow 3s ease-in-out infinite;
  pointer-events: none;
}
.vision-glow {
  background: radial-gradient(circle, rgba(0, 212, 255, 0.15) 0%, transparent 70%);
  animation: iconGlow 3.5s ease-in-out infinite 0.5s;
  pointer-events: none;
}

@keyframes rocketFloat {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  25% { transform: translate(1px, -3px) rotate(2deg); }
  50% { transform: translate(0, -5px) rotate(0deg); }
  75% { transform: translate(-1px, -3px) rotate(-2deg); }
}
@keyframes eyePulse {
  0%, 100% { transform: scale(1); }
  30% { transform: scale(1.08); }
  50% { transform: scaleY(0.3); }
  55% { transform: scaleY(1); }
  70% { transform: scale(1.05); }
}
@keyframes iconGlow {
  0%, 100% { opacity: 0; transform: scale(0.8); }
  50% { opacity: 1; transform: scale(1.2); }
}

/* ── Timeline Animations ── */
.tl-particle {
  animation: tlParticleFloat 4s ease-in-out infinite;
}
.tl-line-fill {
  animation: tlLinePulse 3s ease-in-out infinite;
}
.tl-traveling-dot {
  animation: tlTravelDown 4s linear infinite;
}
.tl-ping {
  animation: tlPing 2.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}
.tl-glow-ring {
  animation: tlGlowRing 2s ease-in-out infinite;
}
.tl-core-dot {
  animation: tlCorePulse 2s ease-in-out infinite;
}
.tl-sparkle {
  animation: tlSparkle 2s ease-in-out infinite;
}
.tl-date-dot {
  animation: tlDateDot 1.5s ease-in-out infinite;
}

@keyframes tlParticleFloat {
  0%, 100% { transform: translateY(0) scale(1); opacity: 0.3; }
  50% { transform: translateY(-20px) scale(1.3); opacity: 0.6; }
}
@keyframes tlLinePulse {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 1; }
}
@keyframes tlTravelDown {
  0% { top: -8%; }
  100% { top: 108%; }
}
@keyframes tlPing {
  0% { transform: scale(0.5); opacity: 0.6; }
  75%, 100% { transform: scale(1.4); opacity: 0; }
}
@keyframes tlGlowRing {
  0%, 100% { transform: scale(1); opacity: 0.4; }
  50% { transform: scale(1.4); opacity: 0.8; }
}
@keyframes tlCorePulse {
  0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(123, 47, 255, 0.3); }
  50% { transform: scale(1.1); box-shadow: 0 0 12px 4px rgba(123, 47, 255, 0.15); }
}
@keyframes tlSparkle {
  0%, 100% { opacity: 0.4; transform: scale(0.8); }
  50% { opacity: 1; transform: scale(1.2); }
}
@keyframes tlDateDot {
  0%, 100% { opacity: 0.5; }
  50% { opacity: 1; transform: scale(1.3); }
}
</style>
