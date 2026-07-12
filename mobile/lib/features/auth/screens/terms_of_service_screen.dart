import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';

class TermsOfServiceScreen extends StatelessWidget {
  const TermsOfServiceScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [AppColors.primary, AppColors.primaryDark, Color(0xFF065F46)],
          ),
        ),
        child: SafeArea(
          child: Column(
            children: [
              // Header with back button
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                child: Row(
                  children: [
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.15),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: Colors.white.withOpacity(0.2)),
                      ),
                      child: IconButton(
                        icon: const Icon(Icons.arrow_back_rounded, color: Colors.white),
                        onPressed: () => context.pop(),
                        padding: const EdgeInsets.all(12),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Text(
                        'Terms of Service',
                        style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                      ),
                    ),
                  ],
                ),
              ),
              // Content
              Expanded(
                child: Container(
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.only(
                      topLeft: Radius.circular(32),
                      topRight: Radius.circular(32),
                    ),
                  ),
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.fromLTRB(28, 32, 28, 28),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildSection(
                          '1. Acceptance of Terms',
                          'By accessing and using Dukarahisi, you accept and agree to be bound by the terms and provisions of this agreement.',
                        ),
                        const SizedBox(height: 24),
                        _buildSection(
                          '2. User Account',
                          'You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account or password.',
                        ),
                        const SizedBox(height: 24),
                        _buildSection(
                          '3. Intellectual Property',
                          'All content included on Dukarahisi, such as text, graphics, logos, images, and software, is the property of Dukarahisi or its content suppliers and is protected by international copyright laws.',
                        ),
                        const SizedBox(height: 24),
                        _buildSection(
                          '4. User Conduct',
                          'You agree not to use the service for any unlawful purpose, to solicit others to perform or participate in any unlawful acts, or to violate any international, federal, provincial, or state regulations.',
                        ),
                        const SizedBox(height: 24),
                        _buildSection(
                          '5. Payment Terms',
                          'All payments are processed through secure mobile payment platforms. By making a purchase, you agree to provide accurate and complete payment information.',
                        ),
                        const SizedBox(height: 24),
                        _buildSection(
                          '6. Privacy Policy',
                          'Your use of Dukarahisi is also subject to our Privacy Policy. Please review our Privacy Policy, which also governs the service and describes how we collect, use, and protect your data.',
                        ),
                        const SizedBox(height: 24),
                        _buildSection(
                          '7. Termination',
                          'We reserve the right to terminate or suspend your account and access to the service at our sole discretion, without prior notice, for conduct that we believe violates these Terms of Service.',
                        ),
                        const SizedBox(height: 24),
                        _buildSection(
                          '8. Limitation of Liability',
                          'In no event shall Dukarahisi be liable for any indirect, incidental, special, consequential, or punitive damages arising out of your access to or use of the service.',
                        ),
                        const SizedBox(height: 24),
                        _buildSection(
                          '9. Governing Law',
                          'These Terms of Service and any separate agreements whereby we provide you services shall be governed by and construed in accordance with the laws of Tanzania.',
                        ),
                        const SizedBox(height: 24),
                        _buildSection(
                          '10. Changes to Terms',
                          'We reserve the right to modify these terms at any time. We will notify users of any material changes by posting the new Terms of Service on this page.',
                        ),
                        const SizedBox(height: 32),
                        Container(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(
                            color: AppColors.primaryLight,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: AppColors.primary.withOpacity(0.2)),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  Icon(Icons.info_rounded, color: AppColors.primary, size: 24),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Text(
                                      'Last Updated',
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.primary,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 8),
                              Text(
                                'July 12, 2026',
                                style: TextStyle(
                                  fontSize: 14,
                                  color: AppColors.textSecondary,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 24),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSection(String title, String content) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: const TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: 8),
        Text(
          content,
          style: const TextStyle(
            fontSize: 15,
            color: AppColors.textSecondary,
            height: 1.5,
          ),
        ),
      ],
    );
  }
}
