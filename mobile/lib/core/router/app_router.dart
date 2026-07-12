import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../features/auth/screens/login_screen.dart';
import '../../features/auth/screens/onboarding_screen.dart';
import '../../features/auth/screens/register_screen.dart';
import '../../features/auth/screens/select_level_screen.dart';
import '../../features/auth/screens/forgot_password_screen.dart';
import '../../features/auth/screens/otp_verification_screen.dart';
import '../../features/auth/screens/terms_of_service_screen.dart';
import '../../features/auth/screens/privacy_policy_screen.dart';
import '../../features/catalogue/screens/home_screen.dart';
import '../../features/profile/screens/profile_screen.dart';
import '../../features/common/screens/not_found_screen.dart';
import '../../features/dashboard/screens/main_screen.dart';
import '../../features/dashboard/screens/dashboard_screen.dart';
import '../../features/more/screens/more_screen.dart';
import '../../features/payments/screens/payments_screen.dart';
import '../../features/reports/screens/reports_screen.dart';
import '../../features/catalog/screens/classes_screen.dart';
import '../../features/catalog/screens/subjects_screen.dart';
import '../../features/catalog/screens/topics_screen.dart';
import '../../features/catalog/screens/materials_screen.dart' as catalog;
import '../../features/catalog/screens/catalog_levels_screen.dart';
import '../../features/catalog/screens/catalog_classes_screen.dart';
import '../../features/catalog/screens/catalog_subjects_screen.dart';
import '../../features/catalog/screens/catalog_topics_screen.dart';
import '../../features/catalog/screens/catalog_materials_screen.dart';
import '../../core/storage/local_cache.dart';
import '../../core/config/constants.dart';

class AppRouter {
  AppRouter._();

  static final _rootNavigatorKey = GlobalKey<NavigatorState>();

  static Future<String> _getInitialLocation() async {
    final onboardingDone = LocalCache.get<bool>(Constants.onboardingCompletedKey) ?? false;
    final isLoggedIn = LocalCache.get<bool>(Constants.isLoggedInKey) ?? false;
    
    if (!onboardingDone) return '/onboarding';
    if (isLoggedIn) return '/main';
    return '/login';
  }

  static GoRouter router() {
    return GoRouter(
      navigatorKey: _rootNavigatorKey,
      initialLocation: '/onboarding',
      redirect: (context, state) async {
        final onboardingDone = LocalCache.get<bool>(Constants.onboardingCompletedKey) ?? false;
        final isLoggedIn = LocalCache.get<bool>(Constants.isLoggedInKey) ?? false;
        final isOnboarding = state.matchedLocation == '/onboarding';
        final isAuthRoute = state.matchedLocation == '/login' || 
                           state.matchedLocation == '/register' ||
                           state.matchedLocation == '/forgot-password' ||
                           state.matchedLocation == '/otp-verification';
        final isMainRoute = state.matchedLocation == '/main';
        
        // If onboarding not done, redirect to onboarding
        if (!onboardingDone && !isOnboarding) return '/onboarding';
        
        // If onboarding done and trying to access onboarding, redirect to login or main
        if (onboardingDone && isOnboarding) return isLoggedIn ? '/main' : '/login';
        
        // If logged in and trying to access auth routes, redirect to main
        if (isLoggedIn && isAuthRoute) return '/main';
        
        // If not logged in and trying to access main, redirect to login
        if (!isLoggedIn && isMainRoute) return '/login';
        
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
          path: '/forgot-password',
          builder: (context, state) => const ForgotPasswordScreen(),
        ),
        GoRoute(
          path: '/otp-verification',
          builder: (context, state) => const OTPVerificationScreen(),
        ),
        GoRoute(
          path: '/terms-of-service',
          builder: (context, state) => const TermsOfServiceScreen(),
        ),
        GoRoute(
          path: '/privacy-policy',
          builder: (context, state) => const PrivacyPolicyScreen(),
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
          path: '/main',
          builder: (context, state) => const MainScreen(),
        ),
        GoRoute(
          path: '/dashboard',
          builder: (context, state) => const DashboardScreen(),
        ),
        GoRoute(
          path: '/more',
          builder: (context, state) => const MoreScreen(),
        ),
        GoRoute(
          path: '/profile',
          builder: (context, state) => const ProfileScreen(),
        ),
        GoRoute(
          path: '/payments',
          builder: (context, state) => const PaymentsScreen(),
        ),
        GoRoute(
          path: '/reports',
          builder: (context, state) => const ReportsScreen(),
        ),
        GoRoute(
          path: '/search',
          builder: (context, state) => const HomeScreen(),
        ),
        GoRoute(
          path: '/wishlist',
          builder: (context, state) => const HomeScreen(),
        ),
        GoRoute(
          path: '/history',
          builder: (context, state) => const HomeScreen(),
        ),
        GoRoute(
          path: '/help',
          builder: (context, state) => const HomeScreen(),
        ),
        GoRoute(
          path: '/contact',
          builder: (context, state) => const HomeScreen(),
        ),
        GoRoute(
          path: '/privacy',
          builder: (context, state) => const PrivacyPolicyScreen(),
        ),
        GoRoute(
          path: '/terms',
          builder: (context, state) => const TermsOfServiceScreen(),
        ),
        GoRoute(
          path: '/about',
          builder: (context, state) => const HomeScreen(),
        ),
        GoRoute(
          path: '/classes',
          builder: (context, state) => const ClassesScreen(),
        ),
        GoRoute(
          path: '/subjects',
          builder: (context, state) => const SubjectsScreen(),
        ),
        GoRoute(
          path: '/topics',
          builder: (context, state) => const TopicsScreen(),
        ),
        GoRoute(
          path: '/materials',
          builder: (context, state) => const catalog.MaterialsScreen(),
        ),
        GoRoute(
          path: '/catalog-levels',
          builder: (context, state) => const CatalogLevelsScreen(),
        ),
        GoRoute(
          path: '/catalog-classes',
          builder: (context, state) => const CatalogClassesScreen(),
        ),
        GoRoute(
          path: '/catalog-subjects',
          builder: (context, state) => const CatalogSubjectsScreen(),
        ),
        GoRoute(
          path: '/catalog-topics',
          builder: (context, state) => const CatalogTopicsScreen(),
        ),
        GoRoute(
          path: '/catalog-materials',
          builder: (context, state) => const CatalogMaterialsScreen(),
        ),
        GoRoute(
          path: '/404',
          builder: (context, state) => const NotFoundScreen(),
        ),
      ],
    );
  }
}
