import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/services/catalog_service.dart';
import '../../../core/network/api_client.dart';
import 'dashboard_screen.dart';
import '../../more/screens/more_screen.dart';
import '../../payments/screens/payments_screen.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _currentIndex = 0;

  final List<Widget> _screens = [
    const DashboardScreen(),
    const MaterialsScreen(),
    const OrdersScreen(),
    const MoreScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 10,
              offset: const Offset(0, -2),
            ),
          ],
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildNavItem(
                  icon: Icons.home_rounded,
                  label: 'Home',
                  index: 0,
                ),
                _buildNavItem(
                  icon: Icons.library_books_rounded,
                  label: 'Materials',
                  index: 1,
                ),
                _buildNavItem(
                  icon: Icons.shopping_bag_rounded,
                  label: 'Orders',
                  index: 2,
                ),
                _buildNavItem(
                  icon: Icons.more_horiz_rounded,
                  label: 'More',
                  index: 3,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem({
    required IconData icon,
    required String label,
    required int index,
  }) {
    final isSelected = _currentIndex == index;
    
    return Expanded(
      child: GestureDetector(
        onTap: () {
          setState(() {
            _currentIndex = index;
          });
        },
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 6),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(
                icon,
                color: isSelected ? AppColors.primary : AppColors.textSecondary,
                size: 22,
              ),
              const SizedBox(height: 2),
              Text(
                label,
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
                  color: isSelected ? AppColors.primary : AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class MaterialsScreen extends StatefulWidget {
  const MaterialsScreen({super.key});

  @override
  State<MaterialsScreen> createState() => _MaterialsScreenState();
}

class _MaterialsScreenState extends State<MaterialsScreen> {
  final List<Map<String, dynamic>> _materialTypes = [
    {
      'title': 'Notes',
      'image': 'assets/icons/notes.png',
      'color': AppColors.primary,
      'route': '/notes',
    },
    {
      'title': 'Books',
      'image': 'assets/icons/books.png',
      'color': const Color(0xFF0EA5E9),
      'route': '/books',
    },
    {
      'title': 'Lesson Plans',
      'image': 'assets/icons/lessonplan.png',
      'color': const Color(0xFF8B5CF6),
      'route': '/lesson-plans',
    },
    {
      'title': 'Syllabus',
      'image': 'assets/images/sylabues.png',
      'color': const Color(0xFFF59E0B),
      'route': '/syllabus',
    },
    {
      'title': 'Scheme of Work',
      'image': 'assets/icons/schemeof work.png',
      'color': const Color(0xFFEC4899),
      'route': '/scheme-of-work',
    },
    {
      'title': 'Logbooks',
      'image': 'assets/icons/logbook.png',
      'color': const Color(0xFF6366F1),
      'route': '/logbooks',
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  const Text(
                    'Study Materials',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ],
              ),
            ),
            Expanded(
              child: GridView.builder(
                padding: const EdgeInsets.all(16),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  crossAxisSpacing: 16,
                  mainAxisSpacing: 16,
                  childAspectRatio: 1.1,
                ),
                itemCount: _materialTypes.length,
                itemBuilder: (context, index) {
                  final materialType = _materialTypes[index];
                  return _buildMaterialTypeCard(materialType);
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMaterialTypeCard(Map<String, dynamic> materialType) {
    final color = materialType['color'] as Color;
    final title = materialType['title'] as String;
    final icon = materialType['icon'] as IconData?;
    final image = materialType['image'] as String?;
    final route = materialType['route'] as String;

    return GestureDetector(
      onTap: () {
        // Navigate to levels screen with material type (remove leading slash)
        final materialType = route.startsWith('/') ? route.substring(1) : route;
        context.push('/catalog-levels', extra: materialType);
      },
      child: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white,
              Colors.grey[50]!,
            ],
          ),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: color.withOpacity(0.2), width: 1),
          boxShadow: [
            BoxShadow(
              color: color.withOpacity(0.08),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              image != null
                  ? Image.asset(
                      image,
                      width: 60,
                      height: 60,
                      fit: BoxFit.contain,
                    )
                  : Icon(
                      icon,
                      color: color,
                      size: 60,
                    ),
              const SizedBox(height: 16),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class OrdersScreen extends StatelessWidget {
  const OrdersScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return const PaymentsScreen();
  }
}

