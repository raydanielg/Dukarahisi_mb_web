class Constants {
  Constants._();

  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String isLoggedInKey = 'is_logged_in';
  static const String onboardingCompletedKey = 'onboarding_completed';
  static const String selectedLevelKey = 'selected_level';
  static const String selectedClassKey = 'selected_class';

  static const int maxRetries = 3;
  static const int connectionTimeout = 30000;
  static const int receiveTimeout = 30000;
}
