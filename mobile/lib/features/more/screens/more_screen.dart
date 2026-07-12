import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';

class MoreScreen extends StatelessWidget {
  const MoreScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Color(0xFF024938),
              Color(0xFF023D30),
              Color(0xFF065F46),
              Color(0xFF024938),
            ],
            stops: [0.0, 0.3, 0.7, 1.0],
          ),
        ),
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 20),
                const Text(
                  'Zaidi',
                  style: TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  'Pata kila kitu unachohitaji',
                  style: TextStyle(
                    fontSize: 14,
                    color: Colors.white.withOpacity(0.8),
                  ),
                ),
                const SizedBox(height: 32),
                _buildSection(
                  'Akaunti',
                  [
                    _buildMenuItem(
                      'Profile',
                      Icons.person_outline,
                      AppColors.primary,
                      () => context.push('/profile'),
                    ),
                    _buildMenuItem(
                      'Malipo',
                      Icons.payment_outlined,
                      AppColors.accent,
                      () => context.push('/payments'),
                    ),
                    _buildMenuItem(
                      'Ripoti',
                      Icons.assessment_outlined,
                      const Color(0xFF0EA5E9),
                      () => context.push('/reports'),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                _buildSection(
                  'Msaada',
                  [
                    _buildMenuItem(
                      'Msaada',
                      Icons.help_outline,
                      const Color(0xFF8B5CF6),
                      () => context.push('/help'),
                    ),
                    _buildMenuItem(
                      'Wasiliana Nasi',
                      Icons.contact_support_outlined,
                      const Color(0xFFEC4899),
                      () => context.push('/contact'),
                    ),
                    _buildMenuItem(
                      'Sera ya Faragha',
                      Icons.privacy_tip_outlined,
                      const Color(0xFF6366F1),
                      () => context.push('/privacy'),
                    ),
                    _buildMenuItem(
                      'Masharti ya Utumiaji',
                      Icons.description_outlined,
                      const Color(0xFF14B8A6),
                      () => context.push('/terms'),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                _buildSection(
                  'Programu',
                  [
                    _buildMenuItem(
                      'Kuhusu',
                      Icons.info_outline,
                      const Color(0xFF64748B),
                      () => context.push('/about'),
                    ),
                    _buildMenuItem(
                      'Toa Rating',
                      Icons.star_outline,
                      const Color(0xFFF59E0B),
                      () {},
                    ),
                    _buildMenuItem(
                      'Toka',
                      Icons.logout,
                      const Color(0xFFEF4444),
                      () => context.push('/login'),
                    ),
                  ],
                ),
                const SizedBox(height: 100),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildSection(String title, List<Widget> children) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white,
            Colors.white.withOpacity(0.95),
          ],
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(0.3), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
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
            const SizedBox(height: 16),
            ...children,
          ],
        ),
      ),
    );
  }

  Widget _buildMenuItem(String label, IconData icon, Color color, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              color.withOpacity(0.05),
              color.withOpacity(0.02),
            ],
          ),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: color.withOpacity(0.15), width: 1),
        ),
        child: Row(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    color,
                    color.withOpacity(0.7),
                  ],
                ),
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: color.withOpacity(0.3),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Icon(icon, color: Colors.white, size: 22),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Text(
                label,
                style: const TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
            ),
            Icon(
              Icons.chevron_right_rounded,
              color: AppColors.textMuted,
              size: 24,
            ),
          ],
        ),
      ),
    );
  }
}
