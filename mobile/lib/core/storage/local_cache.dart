import 'package:hive_flutter/hive_flutter.dart';

class LocalCache {
  static Box? _appBox;

  static Future<void> init() async {
    await Hive.initFlutter();
    _appBox = await Hive.openBox('app_cache');
  }

  static Future<void> set<T>(String key, T value) async {
    await _appBox?.put(key, value);
  }

  static T? get<T>(String key) {
    return _appBox?.get(key) as T?;
  }

  static Future<void> delete(String key) async {
    await _appBox?.delete(key);
  }

  static Future<void> clear() async {
    await _appBox?.clear();
  }
}
