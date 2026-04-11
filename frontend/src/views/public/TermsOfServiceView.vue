<template>
  <div class="min-h-screen bg-white dark:bg-dark-900">
    <!-- Hero -->
    <section class="relative py-16 sm:py-24 overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-brand-violet/5 via-transparent to-brand-cyan/5"></div>
      <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">Terms of Service</h1>
        <p class="text-gray-500 dark:text-gray-400">Last updated: April 11, 2026</p>
      </div>
    </section>

    <!-- Content -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
      <div class="prose prose-lg dark:prose-invert max-w-none space-y-8">

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">1. Acceptance of Terms</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            By accessing or using the Kalapak Code Team website at
            <a href="https://kalapak-team.space" class="text-brand-violet dark:text-brand-cyan hover:underline">kalapak-team.space</a>
            ("the Service"), you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the Service.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">2. Description of Service</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            Kalapak Code Team provides a platform for showcasing development projects, publishing blog articles, and building a community of developers. We offer user accounts, project portfolios, a blog, and team membership applications.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">3. User Accounts</h2>
          <ul class="list-disc pl-6 text-gray-600 dark:text-gray-400 space-y-2">
            <li>You may register using an email/password or through third-party providers (Google, GitHub).</li>
            <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
            <li>You must provide accurate and complete information when creating your account.</li>
            <li>You are responsible for all activities that occur under your account.</li>
            <li>We reserve the right to suspend or terminate accounts that violate these terms.</li>
          </ul>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">4. User Content</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-3">When you submit content (comments, blog posts, project descriptions) to the Service:</p>
          <ul class="list-disc pl-6 text-gray-600 dark:text-gray-400 space-y-2">
            <li>You retain ownership of your original content.</li>
            <li>You grant us a non-exclusive, worldwide license to display and distribute your content on the platform.</li>
            <li>You agree not to post content that is illegal, harmful, threatening, abusive, defamatory, or otherwise objectionable.</li>
            <li>We reserve the right to remove any content that violates these terms.</li>
          </ul>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">5. Intellectual Property</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            The Service, including its design, logos, text, graphics, and code, is owned by Kalapak Code Team and is protected by intellectual property laws. You may not copy, modify, distribute, or reverse-engineer any part of the Service without our prior written consent.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">6. Prohibited Conduct</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-3">You agree not to:</p>
          <ul class="list-disc pl-6 text-gray-600 dark:text-gray-400 space-y-2">
            <li>Use the Service for any unlawful purpose</li>
            <li>Attempt to gain unauthorized access to any part of the Service</li>
            <li>Interfere with or disrupt the Service or its infrastructure</li>
            <li>Upload malicious code, viruses, or harmful content</li>
            <li>Impersonate any person or entity</li>
            <li>Scrape, crawl, or use automated tools to collect data without permission</li>
          </ul>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">7. Third-Party Services</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            The Service integrates with third-party services such as Google and GitHub for authentication. Your use of these services is governed by their respective terms and privacy policies. We are not responsible for the practices of third-party services.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">8. Disclaimer of Warranties</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            The Service is provided "as is" and "as available" without warranties of any kind, either express or implied. We do not guarantee that the Service will be uninterrupted, error-free, or secure.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">9. Limitation of Liability</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            To the fullest extent permitted by law, Kalapak Code Team shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the Service.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">10. Changes to Terms</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            We may update these Terms of Service from time to time. We will notify you of significant changes by posting the new terms on this page and updating the "Last updated" date. Continued use of the Service after changes constitutes acceptance of the revised terms.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">11. Contact Us</h2>
          <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
            If you have any questions about these Terms of Service, please contact us through our
            <router-link to="/contact" class="text-brand-violet dark:text-brand-cyan hover:underline">Contact Page</router-link>.
          </p>
        </div>

      </div>
    </section>
  </div>
</template>

<script setup>
</script>
