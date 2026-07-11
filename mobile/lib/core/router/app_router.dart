import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../features/auth/screens/login_screen.dart';
import '../../features/auth/screens/onboarding_screen.dart';
import '../../features/auth/screens/register_screen.dart';
import '../../features/auth/screens/select_level_screen.dart';
import '../../features/catalogue/screens/home_screen.dart';
import '../../features/profile/screens/profile_screen.dart';
import '../../core/storage/local_cache.dart';
import '../../core/config/constants.dart';

class AppRouter {
  AppRouter._();

  static final _rootNavigatorKey = GlobalKey<NavigatorState>();

  static Future<String> _getInitialLocation() async {
    final onboardingDone = LocalCache.get<bool>(Constants.onboardingCompletedKey) ?? false;
    return onboardingDone ? '/login' : '/onboarding';
  }

  static GoRouter router() {
    return GoRouter(
      navigatorKey: _rootNavigatorKey,
      initialLocation: '/onboarding',
      redirect: (context, state) async {
        final onboardingDone = LocalCache.get<bool>(Constants.onboardingCompletedKey) ?? false;
        final isOnboarding = state.matchedLocation == '/onboarding';
        if (!onboardingDone && !isOnboarding) return '/onboarding';
        if (onboardingDone && isOnboarding) return '/login';
        return null;
      },
      routes: [
        GoRoute(
          path: '/onboarding',
          builder: (context, state) => const OnboardingScreen(),
        ),
        GoRoute(
          path: '/login',
          builder: (context, state) => const LoginScreen(),
        ),
        GoRoute(
          path: '/register',
          builder: (context, state) => const RegisterScreen(),
        ),
        GoRoute(
          path: '/select-level',
          builder: (context, state) => const SelectLevelScreen(),
        ),
        GoRoute(
          path: '/home',
          builder: (context, state) => const HomeScreen(),
        ),
        GoRoute(
          path: '/profile',
          builder: (context, state) => const ProfileScreen(),
        ),
      ],
    );
  }
}
