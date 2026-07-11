import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_colors.dart';

class SelectLevelScreen extends StatefulWidget {
  const SelectLevelScreen({super.key});

  @override
  State<SelectLevelScreen> createState() => _SelectLevelScreenState();
}

class _SelectLevelScreenState extends State<SelectLevelScreen> {
  String? _selectedLevel;
  String? _selectedClass;

  final List<Map<String, dynamic>> _levels = [
    {'name': 'Primary School', 'classes': ['Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7']},
    {'name': 'Secondary School', 'classes': ['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6']},
  ];

  void _continue() {
    if (_selectedLevel != null && _selectedClass != null) {
      context.go('/home');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [AppColors.surface, AppColors.background],
          ),
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const SizedBox(height: 40),
                Text(
                  'Select Your Level',
                  textAlign: TextAlign.center,
                  style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                ),
                const SizedBox(height: 8),
                Text(
                  'Choose your education level and class to personalize your learning experience',
                  textAlign: TextAlign.center,
                  style: Theme.of(context).textTheme.bodyLarge?.copyWith(color: AppColors.textSecondary),
                ),
                const SizedBox(height: 40),
                Expanded(
                  child: ListView.builder(
                    itemCount: _levels.length,
                    itemBuilder: (context, index) {
                      final level = _levels[index];
                      final isSelected = _selectedLevel == level['name'];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          color: AppColors.surface,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: isSelected ? AppColors.primary : AppColors.border,
                            width: isSelected ? 2 : 1,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: AppColors.textPrimary.withOpacity(0.05),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: ExpansionTile(
                          title: Text(
                            level['name'],
                            style: const TextStyle(fontWeight: FontWeight.w600, color: AppColors.textPrimary),
                          ),
                          leading: CircleAvatar(
                            backgroundColor: isSelected ? AppColors.primary : AppColors.primaryLight,
                            child: Icon(
                              index == 0 ? Icons.looks_one : Icons.looks_two,
                              color: isSelected ? Colors.white : AppColors.primary,
                            ),
                          ),
                          initiallyExpanded: isSelected,
                          children: (level['classes'] as List<String>).map((className) {
                            final classSelected = _selectedClass == className && _selectedLevel == level['name'];
                            return ListTile(
                              title: Text(className),
                              trailing: classSelected
                                  ? const Icon(Icons.check_circle, color: AppColors.primary)
                                  : const Icon(Icons.circle_outlined, color: AppColors.border),
                              onTap: () {
                                setState(() {
                                  _selectedLevel = level['name'];
                                  _selectedClass = className;
                                });
                              },
                            );
                          }).toList(),
                        ),
                      );
                    },
                  ),
                ),
                ElevatedButton(
                  onPressed: (_selectedLevel != null && _selectedClass != null) ? _continue : null,
                  child: const Text('Continue'),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
