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

  Future<Map<String, dynamic>> getClasses(int levelId, {String? materialType}) async {
    try {
      String url = '/catalog/classes/$levelId';
      if (materialType != null) {
        url += '?material_type=$materialType';
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
