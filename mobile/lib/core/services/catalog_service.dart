import '../network/api_client.dart';

class CatalogService {
  final ApiClient _apiClient;

  CatalogService(this._apiClient);

  Future<Map<String, dynamic>> getLevels({String? materialType}) async {
    try {
      String url = '/catalog/levels';
      if (materialType != null) {
        url += '?material_type=$materialType';
      }
      final response = await _apiClient.get(url);
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getClasses(int levelId, {String? materialType, dynamic subLevelId}) async {
    try {
      String url = '/catalog/classes/$levelId';
      final params = <String>[];
      if (materialType != null) {
        params.add('material_type=$materialType');
      }
      if (subLevelId != null) {
        params.add('sub_level_id=$subLevelId');
      }
      if (params.isNotEmpty) {
        url += '?${params.join('&')}';
      }
      final response = await _apiClient.get(url);
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getSubjects(int classId, {String? materialType}) async {
    try {
      String url = '/catalog/subjects/$classId';
      if (materialType != null) {
        url += '?material_type=$materialType';
      }
      final response = await _apiClient.get(url);
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getTopics(int subjectId, {String? materialType}) async {
    try {
      String url = '/catalog/topics/$subjectId';
      if (materialType != null) {
        url += '?material_type=$materialType';
      }
      final response = await _apiClient.get(url);
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getMaterials(int topicId, {String? materialType}) async {
    try {
      String url = '/catalog/materials/$topicId';
      if (materialType != null) {
        url += '?material_type=$materialType';
      }
      final response = await _apiClient.get(url);
      return response.data;
    } catch (e) {
      rethrow;
    }
  }
}
