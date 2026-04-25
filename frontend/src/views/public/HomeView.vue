<template>
  <div class="home-page">

    <!-- ═══════════════════ HERO ═══════════════════ -->
    <section class="relative min-h-[85vh] sm:min-h-screen flex items-center justify-center overflow-hidden">
      <!-- Milky Way Galaxy Background -->
      <MilkyWayGalaxy />
      <!-- Animated grid background -->
      <div class="absolute inset-0 hero-grid opacity-[0.03] dark:opacity-[0.05]" />
      <!-- Gradient orbs -->
      <div class="absolute top-1/4 -left-32 w-[300px] sm:w-[500px] h-[300px] sm:h-[500px] rounded-full bg-brand-violet/20 blur-[80px] sm:blur-[120px] animate-float" />
      <div class="absolute bottom-1/4 -right-32 w-[250px] sm:w-[400px] h-[250px] sm:h-[400px] rounded-full bg-brand-cyan/20 blur-[80px] sm:blur-[120px] animate-float" style="animation-delay: 3s" />
      <!-- Floating code particles -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <span v-for="(p, i) in particles" :key="i" class="absolute text-xs font-code select-none"
          :class="p.color" :style="{ left: p.x, top: p.y, opacity: p.opacity, animationDelay: p.delay, animationDuration: p.duration }"
          style="animation: floatParticle ease-in-out infinite">{{ p.text }}</span>
      </div>

      <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <!-- Badge -->
        <div data-aos="fade-down" data-aos-duration="800" class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 mb-6 sm:mb-8 rounded-full border border-brand-violet/20 dark:border-brand-cyan/20 bg-white/60 dark:bg-dark-800/60 backdrop-blur-sm">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
          </span>
          <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Open for collaboration</span>
        </div>

        <!-- Main heading -->
        <h1 data-aos="fade-up" data-aos-duration="1000" class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-sans font-bold mb-4 sm:mb-6 leading-[1.1]">
          <span class="text-gray-900 dark:text-white">Learning to be</span><br />
          <span class="gradient-text">a pillar for one another</span>
        </h1>

        <!-- CTA Buttons -->
        <div data-aos="fade-up" data-aos-delay="400" class="flex flex-col sm:flex-row flex-wrap justify-center gap-3 sm:gap-4 mb-10 sm:mb-16 px-4 sm:px-0">
          <router-link to="/projects" class="group relative inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3.5 sm:py-4 bg-gradient-brand text-white font-semibold rounded-xl shadow-glow hover:shadow-glow-lg transition-all duration-300 hover:-translate-y-0.5 text-sm sm:text-base">
            <span>Explore Projects</span>
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </router-link>
        </div>

      </div>
    </section>

    <!-- ═══════════════════ TRUSTED BY / STATS BAR ═══════════════════ -->
    <section class="relative z-10 -mt-6 sm:-mt-8 stats-section">
      <div class="max-w-6xl mx-auto px-4">
        <div class="stats-shell grid grid-cols-2 md:grid-cols-4 gap-0 bg-white/95 dark:bg-dark-800/80 backdrop-blur-xl rounded-2xl border border-gray-200 dark:border-dark-600 shadow-glass dark:shadow-glass-dark overflow-hidden" data-aos="fade-up">
          <div class="stats-orb stats-orb-violet" />
          <div class="stats-orb stats-orb-cyan" />
          <div v-for="(stat, i) in stats" :key="i" class="group stat-card px-4 sm:px-6 py-6 sm:py-8 text-center hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-colors duration-300 border-gray-200 dark:border-dark-600"
            :class="[i >= 2 ? 'border-t md:border-t-0' : '', i % 2 !== 0 ? '' : '', i > 0 ? 'border-l md:border-l' : '']">
            <div class="stat-shine" />
            <p class="text-2xl sm:text-3xl md:text-4xl font-sans font-bold gradient-text mb-1 group-hover:scale-110 transition-transform duration-300">
              {{ animatedStats[i]?.display || stat.value }}
            </p>
            <p class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase tracking-wider">{{ stat.label }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ WHAT WE DO ═══════════════════ -->
    <section class="py-16 sm:py-20 lg:py-28 relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <!-- Section header -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-16" data-aos="fade-up">
          <div>
            <span class="inline-block text-xs font-code text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// What we do</span>
            <h2 class="text-2xl sm:text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white leading-tight">
              Turning ideas into<br /><span class="gradient-text">reality</span>
            </h2>
          </div>
          <p class="text-gray-500 dark:text-gray-400 max-w-md text-sm leading-relaxed">
            We specialize in building full-stack applications, from concept to deployment, using the latest technologies and best practices.
          </p>
        </div>

        <!-- Service cards -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
          <div v-for="(service, i) in services" :key="i" data-aos="fade-up" :data-aos-delay="i * 100"
            class="group relative p-8 rounded-2xl border border-gray-200 dark:border-dark-600 bg-white/92 dark:bg-dark-800/60 backdrop-blur-sm hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-500 hover:-translate-y-1 hover:shadow-xl dark:hover:shadow-glow/10">
            <!-- Icon -->
            <div class="w-14 h-14 rounded-2xl mb-6 flex items-center justify-center transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3"
              :class="service.bgClass">
              <span v-html="service.icon" />
            </div>
            <h3 class="text-lg font-sans font-bold text-gray-900 dark:text-white mb-3 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">{{ service.title }}</h3>
            <p class="text-sm text-gray-700 dark:text-gray-400 leading-relaxed">{{ service.description }}</p>
            <!-- Hover gradient line -->
            <div class="absolute bottom-0 left-0 h-0.5 w-0 bg-gradient-brand group-hover:w-full transition-all duration-700 rounded-full" />
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ FEATURED PROJECTS ═══════════════════ -->
    <section class="py-16 sm:py-20 lg:py-28 relative z-10 bg-gray-50/50 dark:bg-dark-900/50">

      <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-16" data-aos="fade-up">
          <div>
            <span class="inline-block text-xs font-code text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// Our work</span>
            <h2 class="text-2xl sm:text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white">
              Featured <span class="gradient-text">Projects</span>
            </h2>
          </div>
          <router-link to="/projects" class="group inline-flex items-center gap-2 text-sm font-semibold text-brand-violet dark:text-brand-cyan hover:gap-3 transition-all">
            View all projects
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </router-link>
        </div>

        <div v-if="projects.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          <router-link v-for="(project, i) in projects" :key="project.id" :to="`/projects/${project.slug}`"
            class="group block" data-aos="fade-up" :data-aos-delay="i * 150">
            <div class="relative rounded-2xl overflow-hidden border border-gray-100 dark:border-dark-600 bg-white dark:bg-dark-800 hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl dark:hover:shadow-glow/10">
              <!-- Image -->
              <div class="aspect-[16/10] overflow-hidden bg-gray-100 dark:bg-dark-700">
                <img v-if="project.cover_image" :src="project.cover_image" :alt="project.title"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <div class="w-16 h-16 rounded-2xl bg-gradient-brand/10 flex items-center justify-center">
                    <svg class="w-8 h-8 text-brand-violet/40 dark:text-brand-cyan/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                  </div>
                </div>
              </div>
              <!-- Content -->
              <div class="p-6">
                <div class="flex flex-wrap gap-2 mb-3">
                  <span v-for="tag in (project.tags || []).slice(0, 3)" :key="tag.id"
                    class="px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded-full bg-brand-violet/8 text-brand-violet/80 dark:bg-brand-cyan/8 dark:text-brand-cyan/80">
                    {{ tag.name }}
                  </span>
                </div>
                <h3 class="text-lg font-sans font-bold text-gray-900 dark:text-white mb-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors">
                  {{ project.title }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">{{ project.description }}</p>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-violet dark:text-brand-cyan group-hover:gap-2 transition-all">
                  View project
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </span>
              </div>
            </div>
          </router-link>
        </div>
        <!-- Empty state -->
        <div v-else class="text-center py-20 text-gray-400 dark:text-gray-500" data-aos="fade-up">
          <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          <p class="text-sm">Projects coming soon</p>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ TEAM PREVIEW ═══════════════════ -->
    <section class="py-16 sm:py-20 lg:py-28 relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <!-- Left: Text -->
          <div data-aos="fade-right">
            <span class="inline-block text-xs font-code text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// Our team</span>
            <h2 class="text-2xl sm:text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 leading-tight">
              Meet the people<br />behind the <span class="gradient-text">code</span>
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
              We're a group of passionate students and developers from Phnom Penh, Cambodia. United by our love for clean code and innovative solutions.
            </p>
            <div class="flex items-center gap-6 mb-8">
              <!-- Team avatars stacked -->
              <div class="flex -space-x-3">
                <div v-for="(member, i) in team.slice(0, 4)" :key="i"
                  class="w-12 h-12 rounded-full border-2 border-white dark:border-dark-800 overflow-hidden bg-gradient-brand flex items-center justify-center ring-2 ring-transparent hover:ring-brand-violet dark:hover:ring-brand-cyan transition-all hover:z-10 hover:scale-110">
                  <img v-if="member.avatar" :src="member.avatar" :alt="member.name" class="w-full h-full object-cover" />
                  <span v-else class="text-white text-sm font-bold">{{ member.name?.charAt(0) }}</span>
                </div>
                <div v-if="team.length > 4" class="w-12 h-12 rounded-full border-2 border-white dark:border-dark-800 bg-gray-100 dark:bg-dark-700 flex items-center justify-center">
                  <span class="text-xs font-bold text-gray-500">+{{ team.length - 4 }}</span>
                </div>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ team.length }}+ Members</p>
                <p class="text-xs text-gray-500">Full-stack developers</p>
              </div>
            </div>
            <router-link to="/about" class="group inline-flex items-center gap-2 px-6 py-3 border border-gray-200 dark:border-dark-500 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 hover:border-brand-violet dark:hover:border-brand-cyan hover:bg-brand-violet/5 dark:hover:bg-brand-cyan/5 transition-all">
              About our team
              <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </router-link>
          </div>

          <!-- Right: Values grid -->
          <div class="grid grid-cols-2 gap-4" data-aos="fade-left">
            <div v-for="(val, i) in values" :key="i"
              class="p-6 rounded-2xl border border-gray-200 dark:border-dark-600 bg-white/92 dark:bg-dark-800/60 backdrop-blur-sm hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-300 hover:-translate-y-1"
              :class="i === 0 ? 'col-span-2' : ''">
              <div class="w-10 h-10 rounded-xl mb-4 flex items-center justify-center" :class="val.bgClass">
                <span class="text-lg">{{ val.icon }}</span>
              </div>
              <h4 class="font-sans font-bold text-gray-900 dark:text-white mb-1.5">{{ val.title }}</h4>
              <p class="text-xs text-gray-700 dark:text-gray-400 leading-relaxed">{{ val.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ LATEST BLOG ═══════════════════ -->
    <section class="py-16 sm:py-20 lg:py-28 relative z-10 bg-gray-50/50 dark:bg-dark-900/50">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-16" data-aos="fade-up">
          <div>
            <span class="inline-block text-xs font-code text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// Blog</span>
            <h2 class="text-2xl sm:text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white">
              Latest <span class="gradient-text">Articles</span>
            </h2>
          </div>
          <router-link to="/blog" class="group inline-flex items-center gap-2 text-sm font-semibold text-brand-violet dark:text-brand-cyan hover:gap-3 transition-all">
            Read all articles
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </router-link>
        </div>

        <div v-if="posts.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          <router-link v-for="(post, i) in posts" :key="post.id" :to="`/blog/${post.slug}`"
            class="group block" data-aos="fade-up" :data-aos-delay="i * 150">
            <div class="relative rounded-2xl overflow-hidden border border-gray-100 dark:border-dark-600 bg-white dark:bg-dark-800 hover:border-brand-violet/30 dark:hover:border-brand-cyan/30 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl dark:hover:shadow-glow/10 h-full flex flex-col">
              <!-- Cover image -->
              <div v-if="post.cover_image" class="aspect-[16/9] overflow-hidden">
                <img :src="post.cover_image" :alt="post.title" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
              </div>
              <div class="p-6 flex flex-col flex-1">
                <div class="flex items-center gap-3 mb-4">
                  <span v-if="post.category?.name" class="px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded-full bg-brand-violet/8 text-brand-violet/80 dark:bg-brand-cyan/8 dark:text-brand-cyan/80">
                    {{ post.category.name }}
                  </span>
                  <span class="text-[11px] text-gray-400">{{ formatDate(post.published_at) }}</span>
                </div>
                <h3 class="text-lg font-sans font-bold text-gray-900 dark:text-white mb-2 group-hover:text-brand-violet dark:group-hover:text-brand-cyan transition-colors line-clamp-2">
                  {{ post.title }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 mb-4 flex-1">{{ post.excerpt }}</p>
                <div class="flex items-center gap-3 mt-auto pt-4 border-t border-gray-100 dark:border-dark-600">
                  <div v-if="post.author" class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-gradient-brand flex items-center justify-center overflow-hidden">
                      <img v-if="post.author.avatar" :src="post.author.avatar" class="w-full h-full object-cover" />
                      <span v-else class="text-white text-[10px] font-bold">{{ post.author.name?.charAt(0) }}</span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ post.author.name }}</span>
                  </div>
                  <span v-if="post.reading_time" class="text-[10px] text-gray-400 ml-auto">{{ post.reading_time }} min read</span>
                </div>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- ═══════════════════ TECH STACK — MARQUEE ═══════════════════ -->
    <section class="tech-section relative z-10">

      <!-- Glow orbs -->
      <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-[400px] h-[400px] rounded-full bg-brand-violet/[0.07] blur-[120px] pointer-events-none" />
      <div class="absolute top-1/2 right-1/4 -translate-y-1/2 w-[350px] h-[350px] rounded-full bg-brand-cyan/[0.07] blur-[120px] pointer-events-none" />

      <div class="max-w-7xl mx-auto px-4 text-center mb-14" data-aos="fade-up">
        <span class="inline-block text-xs font-code text-brand-violet dark:text-brand-cyan uppercase tracking-widest mb-3">// Technologies</span>
        <h2 class="text-3xl md:text-5xl font-sans font-bold text-gray-900 dark:text-white mb-4">
          Our <span class="gradient-text">Tech Stack</span>
        </h2>
        <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto text-sm">The tools and frameworks we use to build exceptional digital products.</p>
      </div>

      <!-- Marquee wrapper -->
      <div class="marquee-wrapper" data-aos="fade-up" data-aos-delay="200">
        <!-- Fade masks -->
        <div class="marquee-mask-left" />
        <div class="marquee-mask-right" />

        <div class="marquee-track">
          <!-- Original set -->
          <div v-for="(tech, i) in techStack" :key="'a' + i" class="tech-card group">
            <div class="tech-icon-wrapper">
              <img :src="tech.logo" :alt="tech.name" class="tech-logo" loading="lazy" />
            </div>
            <span class="tech-name">{{ tech.name }}</span>
          </div>
          <!-- Duplicate for seamless loop -->
          <div v-for="(tech, i) in techStack" :key="'b' + i" class="tech-card group" aria-hidden="true">
            <div class="tech-icon-wrapper">
              <img :src="tech.logo" :alt="tech.name" class="tech-logo" loading="lazy" />
            </div>
            <span class="tech-name">{{ tech.name }}</span>
          </div>
        </div>
      </div>
    </section>


  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { publicApi } from '@/services/api'
import dayjs from 'dayjs'
import MilkyWayGalaxy from '@/components/common/MilkyWayGalaxy.vue'

const projects = ref([])
const posts = ref([])
const team = ref([])

const stats = [
  { value: '4+', label: 'Team Members' },
  { value: '10+', label: 'Projects' },
  { value: '100%', label: 'Passion' },
  { value: '∞', label: 'Lines of Code' },
]
const animatedStats = ref(stats.map((stat) => ({ ...stat, display: stat.value })))

const services = [
  {
    title: 'Web Development',
    description: 'Modern, responsive web applications built with Vue.js, Laravel, and cutting-edge frameworks for optimal user experience.',
    bgClass: 'bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400',
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A8.966 8.966 0 013 12c0-1.264.26-2.467.732-3.558"/></svg>',
  },
  {
    title: 'Mobile Apps',
    description: 'Cross-platform mobile applications with Flutter, delivering native performance on iOS and Android from a single codebase.',
    bgClass: 'bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400',
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>',
  },
  {
    title: 'API & Backend',
    description: 'Scalable RESTful APIs and robust backend systems with Laravel, PostgreSQL, and Docker for enterprise-grade reliability.',
    bgClass: 'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400',
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z"/></svg>',
  },
  {
    title: 'UI/UX Design',
    description: 'Beautiful, intuitive interfaces designed with Tailwind CSS, focusing on accessibility, responsiveness, and delightful interactions.',
    bgClass: 'bg-pink-100 dark:bg-pink-900/20 text-pink-700 dark:text-pink-400',
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>',
  },
  {
    title: 'DevOps & Cloud',
    description: 'Containerized deployments with Docker, CI/CD pipelines, and cloud infrastructure management for seamless operations.',
    bgClass: 'bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400',
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z"/></svg>',
  },
  {
    title: 'Open Source',
    description: 'Contributing to the developer community with open-source projects, libraries, and tools that solve real problems.',
    bgClass: 'bg-cyan-100 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-400',
    icon: '<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/></svg>',
  },
]

const values = [
  { icon: '🚀', title: 'Innovation First', desc: 'We push boundaries with modern tech, always exploring new solutions to challenge the status quo.', bgClass: 'bg-brand-violet/15 dark:bg-brand-violet/20' },
  { icon: '🤝', title: 'Collaboration', desc: 'Open-source mindset, team-driven development.', bgClass: 'bg-brand-cyan/20 dark:bg-brand-cyan/20' },
  { icon: '🎓', title: 'Continuous Learning', desc: 'Always growing through mentorship & practice.', bgClass: 'bg-amber-200 dark:bg-amber-900/20' },
  { icon: '🇰🇭', title: 'Cambodia Proud', desc: 'Building world-class tech from Phnom Penh.', bgClass: 'bg-emerald-200 dark:bg-emerald-900/20' },
]

const techStack = [
  { name: 'Vue.js', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg' },
  { name: 'Laravel', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-original.svg' },
  { name: 'Tailwind CSS', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg' },
  { name: 'PostgreSQL', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/postgresql/postgresql-original.svg' },
  { name: 'Docker', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg' },
  { name: 'Flutter', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/flutter/flutter-original.svg' },
  { name: 'Node.js', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg' },
  { name: 'Python', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg' },
  { name: 'TypeScript', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg' },
  { name: 'Nginx', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nginx/nginx-original.svg' },
  { name: 'Git', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg' },
  { name: 'GitHub', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/github/github-original.svg' },
]

function seededUnit(seed) {
  const x = Math.sin(seed * 999.91) * 10000
  return x - Math.floor(x)
}

function fixed(value, decimals) {
  return value.toFixed(decimals)
}

const particles = Array.from({ length: 20 }, (_, i) => {
  const token = [
    '<div>', '</>', '{...}', '( )', '[ ]', 'fn()', '===', '&&', '=>',
    '/**/', '<?php', 'npm', 'git', '::', '++', '0x', '#!', '~/',
  ][i % 18]

  return {
    text: token,
    x: `${fixed(seededUnit(i + 1) * 100, 4)}%`,
    y: `${fixed(seededUnit(i + 101) * 100, 4)}%`,
    opacity: fixed(0.06 + seededUnit(i + 201) * 0.08, 6),
    delay: `${fixed(seededUnit(i + 301) * 8, 4)}s`,
    duration: `${fixed(6 + seededUnit(i + 401) * 8, 4)}s`,
    color: i % 2 === 0 ? 'text-brand-violet/30 dark:text-brand-violet/20' : 'text-brand-cyan/30 dark:text-brand-cyan/20',
  }
})

function formatDate(date) {
  return date ? dayjs(date).format('MMM D, YYYY') : ''
}

function animateStatValue(index, finalValue) {
  const matched = finalValue.match(/^(\d+)(.*)$/)
  if (!matched) {
    animatedStats.value[index].display = finalValue
    return
  }

  const target = Number.parseInt(matched[1], 10)
  const suffix = matched[2] || ''
  const duration = 1300
  const start = performance.now()

  const tick = (now) => {
    const progress = Math.min((now - start) / duration, 1)
    // Ease out for a smoother finish
    const eased = 1 - Math.pow(1 - progress, 3)
    const current = Math.max(1, Math.round(target * eased))
    animatedStats.value[index].display = `${current}${suffix}`

    if (progress < 1) {
      requestAnimationFrame(tick)
    } else {
      animatedStats.value[index].display = finalValue
    }
  }

  requestAnimationFrame(tick)
}

function runStatsAnimation() {
  stats.forEach((stat, index) => {
    setTimeout(() => animateStatValue(index, stat.value), 150 * index)
  })
}

onMounted(async () => {
  try {
    const [projectsRes, postsRes, teamRes] = await Promise.all([
      publicApi.getProjects({ per_page: 3, is_featured: true }),
      publicApi.getBlogPosts({ per_page: 3 }),
      publicApi.getTeam(),
    ])
    projects.value = projectsRes.data.data || []
    posts.value = postsRes.data.data || []
    team.value = teamRes.data.data || teamRes.data || []
  } catch {
    // Silently fail for public page
  }
  runStatsAnimation()
})
</script>

<style scoped>
.hero-grid {
  background-image:
    linear-gradient(rgba(123, 47, 255, 0.1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(123, 47, 255, 0.1) 1px, transparent 1px);
  background-size: 60px 60px;
}

@keyframes floatParticle {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  25% { transform: translateY(-15px) rotate(2deg); }
  50% { transform: translateY(-25px) rotate(-1deg); }
  75% { transform: translateY(-10px) rotate(1deg); }
}

/* ── Modern Stats Bar Effects ── */
.stats-section {
  isolation: isolate;
}

.stats-shell {
  position: relative;
  box-shadow:
    0 14px 35px rgba(15, 23, 42, 0.08),
    0 2px 10px rgba(15, 23, 42, 0.05);
}

.stats-shell::before {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
  background: linear-gradient(120deg, transparent 0%, rgba(255, 255, 255, 0.55) 45%, transparent 65%);
  transform: translateX(-130%);
  animation: statsSweep 9s ease-in-out infinite;
}

.dark .stats-shell::before {
  background: linear-gradient(120deg, transparent 0%, rgba(255, 255, 255, 0.08) 45%, transparent 65%);
}

.stats-orb {
  position: absolute;
  width: 220px;
  height: 220px;
  border-radius: 999px;
  filter: blur(70px);
  opacity: 0.25;
  pointer-events: none;
  animation: statOrbFloat 7s ease-in-out infinite;
}

.stats-orb-violet {
  top: -70px;
  left: -40px;
  background: rgba(123, 47, 255, 0.35);
}

.stats-orb-cyan {
  bottom: -80px;
  right: -30px;
  background: rgba(0, 212, 255, 0.32);
  animation-delay: 2.5s;
}

.stat-card {
  position: relative;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.88);
  animation: statCardPop 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.dark .stat-card {
  background: transparent;
}

.stat-card:nth-child(3) { animation-delay: 90ms; }
.stat-card:nth-child(4) { animation-delay: 170ms; }
.stat-card:nth-child(5) { animation-delay: 250ms; }
.stat-card:nth-child(6) { animation-delay: 330ms; }

.stat-shine {
  position: absolute;
  top: 0;
  left: -150%;
  width: 70%;
  height: 100%;
  pointer-events: none;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
  transform: skewX(-22deg);
  transition: left 700ms ease;
}

.dark .stat-shine {
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent);
}

.stat-card:hover .stat-shine {
  left: 180%;
}

@keyframes statCardPop {
  0% { opacity: 0; transform: translateY(16px) scale(0.98); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes statsSweep {
  0%, 70%, 100% { transform: translateX(-130%); }
  35% { transform: translateX(130%); }
}

@keyframes statOrbFloat {
  0%, 100% { transform: translateY(0px) scale(1); }
  50% { transform: translateY(-12px) scale(1.06); }
}

/* ── Tech Stack Marquee ── */
.tech-section {
  --section-bg: #f8fafc;
  background: var(--section-bg);
  padding: 60px 0 70px;
}
@media (min-width: 640px) {
  .tech-section {
    padding: 90px 0 100px;
  }
}
:root.dark .tech-section,
.dark .tech-section {
  background: #020024;
}

.marquee-wrapper {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.marquee-mask-left,
.marquee-mask-right {
  position: absolute;
  top: 0;
  bottom: 0;
  width: 120px;
  z-index: 2;
  pointer-events: none;
}
.marquee-mask-left {
  left: 0;
  background: linear-gradient(to right, #f8fafc, transparent);
}
.marquee-mask-right {
  right: 0;
  background: linear-gradient(to left, #f8fafc, transparent);
}
:root.dark .marquee-mask-left,
.dark .marquee-mask-left {
  background: linear-gradient(to right, #020024, transparent);
}
:root.dark .marquee-mask-right,
.dark .marquee-mask-right {
  background: linear-gradient(to left, #020024, transparent);
}

.marquee-track {
  display: flex;
  gap: 20px;
  width: max-content;
  animation: marquee 40s linear infinite;
  padding: 12px 0;
}
.marquee-wrapper:hover .marquee-track {
  animation-play-state: paused;
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.tech-card {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  width: 120px;
  padding: 24px 16px 20px;
  border-radius: 20px;
  background: white;
  border: 1px solid rgba(0, 0, 0, 0.06);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03);
  transition: transform 0.35s cubic-bezier(0.22,1,0.36,1), border-color 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
  cursor: default;
}
:root.dark .tech-card,
.dark .tech-card {
  background: rgba(13, 10, 62, 0.7);
  border-color: rgba(42, 37, 112, 0.5);
  box-shadow: 0 2px 12px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.03);
}
.tech-card:hover {
  transform: scale(1.08) translateY(-4px);
  border-color: rgba(123, 47, 255, 0.3);
  box-shadow: 0 8px 30px rgba(123, 47, 255, 0.12), 0 2px 8px rgba(0,0,0,0.06);
}
:root.dark .tech-card:hover,
.dark .tech-card:hover {
  background: rgba(21, 18, 72, 0.85);
  border-color: rgba(123, 47, 255, 0.45);
  box-shadow: 0 8px 40px rgba(123, 47, 255, 0.18), 0 0 0 1px rgba(123,47,255,0.1);
}

.tech-icon-wrapper {
  width: 52px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 14px;
  background: #f1f5f9;
  transition: background 0.3s ease;
}
:root.dark .tech-icon-wrapper,
.dark .tech-icon-wrapper {
  background: rgba(42, 37, 112, 0.35);
}
.tech-card:hover .tech-icon-wrapper {
  background: rgba(123, 47, 255, 0.08);
}
:root.dark .tech-card:hover .tech-icon-wrapper,
.dark .tech-card:hover .tech-icon-wrapper {
  background: rgba(123, 47, 255, 0.15);
}

.tech-logo {
  width: 32px;
  height: 32px;
  object-fit: contain;
  transition: filter 0.3s ease, transform 0.3s ease;
}
.tech-card:hover .tech-logo {
  transform: scale(1.1);
  filter: drop-shadow(0 0 8px rgba(123, 47, 255, 0.25));
}
:root.dark .tech-card:hover .tech-logo,
.dark .tech-card:hover .tech-logo {
  filter: drop-shadow(0 0 10px rgba(0, 212, 255, 0.35));
}

.tech-name {
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.01em;
  color: #374151;
  white-space: nowrap;
}
:root.dark .tech-name,
.dark .tech-name {
  color: #d1d5db;
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  .marquee-track {
    animation: none;
  }
  .stats-shell::before,
  .stats-orb,
  .stat-card {
    animation: none;
  }
  .stat-shine {
    display: none;
  }
}
</style>
