<template>
  <div class="min-h-screen flex bg-dark-900 relative overflow-hidden">

    <!-- Left Panel — Branding (hidden on mobile) -->
    <div class="hidden lg:flex lg:w-[45%] xl:w-[50%] relative flex-col justify-between p-10 xl:p-16 overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-dark-900 via-dark-800 to-brand-violet/20"></div>
      <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-violet/10 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/4"></div>
      <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-brand-cyan/10 rounded-full blur-[100px] translate-y-1/3 -translate-x-1/4"></div>
      <!-- Grid pattern -->
      <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

      <!-- Floating code snippets -->
      <div class="absolute top-[15%] right-[10%] font-code text-xs text-brand-violet/30 animate-float">
        <div class="space-y-1">
          <div>&lt;template&gt;</div>
          <div class="pl-4">&lt;div class="hero"&gt;</div>
          <div class="pl-8">&lt;h1&gt;Welcome&lt;/h1&gt;</div>
          <div class="pl-4">&lt;/div&gt;</div>
          <div>&lt;/template&gt;</div>
        </div>
      </div>
      <div class="absolute bottom-[20%] left-[8%] font-code text-xs text-brand-cyan/25" style="animation: float 8s ease-in-out infinite reverse">
        <div class="space-y-1">
          <div>const team = {</div>
          <div class="pl-4">name: 'Kalapak',</div>
          <div class="pl-4">passion: '∞',</div>
          <div class="pl-4">code: true</div>
          <div>};</div>
        </div>
      </div>

      <!-- Top — Logo -->
      <div class="relative z-10">
        <router-link to="/" class="inline-flex items-center gap-3 group">
          <img src="https://res.cloudinary.com/di1hdlb8k/image/upload/v1775702762/Logo_vmyexl.png" alt="Kalapak Logo" class="w-12 h-12 rounded-xl object-contain shadow-lg shadow-brand-violet/25 group-hover:shadow-brand-violet/40 transition-shadow" />
          <div>
            <h1 class="text-xl font-code font-bold text-white">Kalapak</h1>
            <p class="text-xs text-gray-400 tracking-wider">CODE TEAM</p>
          </div>
        </router-link>
      </div>

      <!-- Center — Headline -->
      <div class="relative z-10 space-y-6">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-violet/10 border border-brand-violet/20">
          <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
          <span class="text-xs font-code text-brand-cyan tracking-wider">OPEN SOURCE</span>
        </div>
        <h2 class="text-4xl xl:text-5xl font-jakarta font-bold text-white leading-tight">
          Build the future, <br>
          <span class="gradient-text">one commit</span><br>
          at a time.
        </h2>
        <p class="text-gray-400 text-lg leading-relaxed max-w-md">
          Join a passionate team of developers from Cambodia building innovative, open-source projects that make a difference.
        </p>

        <!-- Stats -->
        <div class="flex items-center gap-8 pt-4">
          <div>
            <div class="text-2xl font-bold text-white">10+</div>
            <div class="text-xs text-gray-500 uppercase tracking-wider">Projects</div>
          </div>
          <div class="w-px h-10 bg-dark-600"></div>
          <div>
            <div class="text-2xl font-bold text-white">4</div>
            <div class="text-xs text-gray-500 uppercase tracking-wider">Members</div>
          </div>
          <div class="w-px h-10 bg-dark-600"></div>
          <div>
            <div class="text-2xl font-bold text-white">🇰🇭</div>
            <div class="text-xs text-gray-500 uppercase tracking-wider">Cambodia</div>
          </div>
        </div>
      </div>

      <!-- Bottom — Quote -->
      <div class="relative z-10">
        <blockquote class="border-l-2 border-brand-violet/40 pl-4">
          <p class="text-gray-400 text-sm italic leading-relaxed">
            "ជីវិតដែលរស់នៅដើម្បីអ្នកដទៃ ទើបជាជីវិតដែលមានតម្លៃ"
          </p>
          <footer class="mt-2 text-xs text-gray-500">
            — A life lived for others is the only life worth living
          </footer>
        </blockquote>
      </div>
    </div>

    <!-- Right Panel — Form -->
    <div class="w-full lg:w-[55%] xl:w-[50%] flex items-center justify-center relative">
      <!-- Subtle background for right panel -->
      <div class="absolute inset-0 bg-dark-900">
        <StarfieldBg />
      </div>

      <!-- Mobile top bar -->
      <div class="lg:hidden absolute top-0 left-0 right-0 p-6 z-20">
        <router-link to="/" class="inline-flex items-center gap-3 group">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-violet to-brand-cyan flex items-center justify-center shadow-lg shadow-brand-violet/25">
            <span class="text-white font-code font-bold text-sm">&lt;/&gt;</span>
          </div>
          <div>
            <h1 class="text-lg font-code font-bold text-white">Kalapak</h1>
            <p class="text-[10px] text-gray-400 tracking-wider">CODE TEAM</p>
          </div>
        </router-link>
      </div>

      <!-- Form Container -->
      <div class="relative z-10 w-full max-w-md px-6 sm:px-8 py-20 lg:py-8">
        <router-view v-slot="{ Component }">
          <transition name="auth-fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </div>
    </div>
  </div>
</template>

<script setup>
import StarfieldBg from '@/components/common/StarfieldBg.vue'
</script>

<style scoped>
.auth-fade-enter-active,
.auth-fade-leave-active {
  transition: all 0.3s ease;
}
.auth-fade-enter-from {
  opacity: 0;
  transform: translateY(12px);
}
.auth-fade-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
}
</style>
