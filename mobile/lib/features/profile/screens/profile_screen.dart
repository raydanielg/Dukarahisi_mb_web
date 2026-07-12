import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/services/auth_service.dart';
import '../../../core/network/api_client.dart';
import '../../../core/storage/secure_store.dart';
import '../../../core/storage/local_cache.dart';
import '../../../core/config/constants.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  late final AuthService _authService;
  Map<String, dynamic>? _userInfo;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _authService = AuthService(ApiClient());
    _loadUserInfo();
  }

  Future<void> _loadUserInfo() async {
    try {
      final response = await _authService.me();
      if (mounted) {
        setState(() {
          _userInfo = response['data'];
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _loading = false);
      }
    }
  }

  Future<void> _logout() async {
    await _authService.logout();
    await SecureStore.deleteToken();
    await LocalCache.delete(Constants.isLoggedInKey);
    if (mounted) {
      context.go('/login');
    }
  }

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
          child: _loading
              ? const Center(child: CircularProgressIndicator(color: Colors.white))
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      const SizedBox(height: 20),
                      Container(
                        width: 120,
                        height: 120,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          gradient: const LinearGradient(
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                            colors: [AppColors.primary, AppColors.primaryDark],
                          ),
                          border: Border.all(color: Colors.white, width: 3),
                          boxShadow: [
                            BoxShadow(
                              color: AppColors.primary.withOpacity(0.3),
                              blurRadius: 20,
                              offset: const Offset(0, 10),
                            ),
                          ],
                        ),
                        child: Center(
                          child: Text(
                            _userInfo?['name']?.toString().substring(0, 1).toUpperCase() ?? 'U',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 48,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 24),
                      Text(
                        _userInfo?['name']?.toString() ?? 'User',
                        style: const TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        _userInfo?['email']?.toString() ?? '',
                        style: TextStyle(
                          fontSize: 14,
                          color: Colors.white.withOpacity(0.8),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppColors.accent.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: AppColors.accent, width: 1),
                        ),
                        child: Text(
                          _userInfo?['role']?.toString().toUpperCase() ?? 'USER',
                          style: const TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: AppColors.accent,
                          ),
                        ),
                      ),
                      const SizedBox(height: 40),
                      _buildProfileSection(
                        'Maelezo ya Akaunti',
                        [
                          _ProfileTile(
                            icon: Icons.phone_outlined,
                            title: 'Namba ya Simu',
                            subtitle: _userInfo?['phone_number']?.toString() ?? 'Haijawekwa',
                            color: AppColors.primary,
                          ),
                          _ProfileTile(
                            icon: Icons.email_outlined,
                            title: 'Barua Pepe',
                            subtitle: _userInfo?['email']?.toString() ?? '',
                            color: const Color(0xFF0EA5E9),
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),
                      _buildProfileSection(
                        'Usalama',
                        [
                          _ProfileTile(
                            icon: Icons.edit_outlined,
                            title: 'Hariri Profile',
                            subtitle: 'Badilisha maelezo yako',
                            color: const Color(0xFF8B5CF6),
                            onTap: () {},
                          ),
                          _ProfileTile(
                            icon: Icons.lock_outline,
                            title: 'Badilisha Nenosiri',
                            subtitle: 'Weka nenosiri mpya',
                            color: const Color(0xFFEC4899),
                            onTap: () {},
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),
                      _buildProfileSection(
                        'Mengine',
                        [
                          _ProfileTile(
                            icon: Icons.notifications_outlined,
                            title: 'Mipangilio ya Arifa',
                            subtitle: 'Dhibiti arifa zako',
                            color: const Color(0xFFF59E0B),
                            onTap: () {},
                          ),
                          _ProfileTile(
                            icon: Icons.help_outline,
                            title: 'Msaada',
                            subtitle: 'Pata msaada',
                            color: const Color(0xFF14B8A6),
                            onTap: () => context.push('/help'),
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),
                      _ProfileTile(
                        icon: Icons.logout,
                        title: 'Toka',
                        subtitle: 'Funga akaunti yako',
                        color: AppColors.error,
                        onTap: _logout,
                      ),
                      const SizedBox(height: 100),
                    ],
                  ),
                ),
        ),
      ),
    );
  }

  Widget _buildProfileSection(String title, List<Widget> children) {
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
}

class _ProfileTile extends StatelessWidget {
  final IconData icon;
  final String title;
  final String? subtitle;
  final Color? color;
  final VoidCallback? onTap;

  const _ProfileTile({
    required this.icon,
    required this.title,
    this.subtitle,
    this.color,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
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
              (color ?? AppColors.primary).withOpacity(0.05),
              (color ?? AppColors.primary).withOpacity(0.02),
            ],
          ),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: (color ?? AppColors.primary).withOpacity(0.15), width: 1),
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
                    color ?? AppColors.primary,
                    (color ?? AppColors.primary).withOpacity(0.7),
                  ],
                ),
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: (color ?? AppColors.primary).withOpacity(0.3),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Icon(icon, color: Colors.white, size: 22),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  if (subtitle != null)
                    Text(
                      subtitle!,
                      style: TextStyle(
                        fontSize: 12,
                        color: AppColors.textSecondary,
                      ),
                    ),
                ],
              ),
            ),
            if (onTap != null)
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
