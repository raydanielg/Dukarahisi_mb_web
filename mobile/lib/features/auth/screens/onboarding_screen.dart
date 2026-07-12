import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/storage/local_cache.dart';
import '../../../core/config/constants.dart';
import '../../../core/theme/app_colors.dart';

class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key});

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  final PageController _controller = PageController();
  int _currentIndex = 0;

  final List<_OnboardingPage> _pages = const [
    _OnboardingPage(
      icon: 'assets/icons/easy.png',
      title: 'Welcome to Dukarahisi',
      description: 'Your digital learning companion. Access notes, books, and lesson materials anytime, anywhere.',
      badge: 'Learning Made Easy',
    ),
    _OnboardingPage(
      icon: 'assets/icons/subjects.png',
      title: 'All Subjects Covered',
      description: 'Find well-organized notes for Mathematics, Sciences, Languages, and more for every level.',
      badge: 'Comprehensive',
    ),
    _OnboardingPage(
      icon: 'assets/icons/payment.png',
      title: 'Simple Mobile Payment',
      description: 'Purchase materials instantly using mobile money. Secure, fast, and reliable.',
      badge: 'Easy Payment',
    ),
    _OnboardingPage(
      icon: 'assets/icons/flexible.png',
      title: 'Learn Anywhere',
      description: 'Study at your own pace, whether at home, school, or on the go. Your classroom is always with you.',
      badge: 'Flexible',
    ),
    _OnboardingPage(
      icon: 'assets/icons/getstartd.png',
      title: 'Start Your Journey',
      description: 'Join thousands of students using Dukarahisi to excel in their studies. Let\'s get started!',
      badge: 'Get Started',
    ),
  ];

  void _next() {
    if (_currentIndex < _pages.length - 1) {
      _controller.nextPage(duration: const Duration(milliseconds: 400), curve: Curves.easeInOut);
    } else {
      _finish();
    }
  }

  void _finish() async {
    await LocalCache.set(Constants.onboardingCompletedKey, true);
    if (mounted) context.go('/login');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: AppColors.primary.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.auto_stories, color: AppColors.primary, size: 24),
                  ),
                  const SizedBox(width: 12),
                  const Text('Dukarahisi', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.primary)),
                  const Spacer(),
                  GestureDetector(
                    onTap: _finish,
                    child: const Text('Skip', style: TextStyle(fontSize: 14, color: AppColors.textSecondary, fontWeight: FontWeight.w500)),
                  ),
                ],
              ),
            ),
            Expanded(
              child: PageView.builder(
                controller: _controller,
                onPageChanged: (i) => setState(() => _currentIndex = i),
                itemCount: _pages.length,
                itemBuilder: (_, i) => _SlideContent(slide: _pages[i]),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: List.generate(_pages.length, (i) => AnimatedContainer(
                      duration: const Duration(milliseconds: 300),
                      margin: const EdgeInsets.symmetric(horizontal: 4),
                      width: _currentIndex == i ? 24 : 8,
                      height: 8,
                      decoration: BoxDecoration(
                        color: _currentIndex == i ? AppColors.primary : AppColors.border,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    )),
                  ),
                  const SizedBox(height: 28),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          onPressed: _finish,
                          style: OutlinedButton.styleFrom(
                            side: const BorderSide(color: AppColors.border),
                            padding: const EdgeInsets.symmetric(vertical: 16),
                          ),
                          child: const Text('Skip', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        flex: 2,
                        child: ElevatedButton(
                          onPressed: _next,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                          ),
                          child: Text(_currentIndex == _pages.length - 1 ? 'Get Started' : 'Next', style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

}

class _OnboardingPage {
  final String icon;
  final String title;
  final String description;
  final String badge;

  const _OnboardingPage({
    required this.icon,
    required this.title,
    required this.description,
    required this.badge,
  });
}

class _SlideContent extends StatelessWidget {
  final _OnboardingPage slide;
  const _SlideContent({required this.slide});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        children: [
          const SizedBox(height: 20),
          Expanded(
            child: Center(
              child: SizedBox(
                width: 200,
                height: 200,
                child: Image.asset(
                  slide.icon,
                  fit: BoxFit.contain,
                  errorBuilder: (context, error, stackTrace) {
                    return const Icon(Icons.auto_stories, size: 100, color: AppColors.primary);
                  },
                ),
              ),
            ),
          ),
          const SizedBox(height: 24),
          Text(
            'Welcome to Dukarahisi',
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 26, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
          ),
          const SizedBox(height: 12),
          Text(
            slide.description,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 15, color: AppColors.textSecondary, height: 1.5),
          ),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: AppColors.primaryLight,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppColors.primary.withOpacity(0.3), width: 1),
            ),
            child: Text(
              slide.badge,
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.primary),
            ),
          ),
        ],
      ),
    );
  }
}
