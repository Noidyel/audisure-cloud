import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'status_result_screen.dart';

class StatusScreen extends StatefulWidget {
  const StatusScreen({super.key});

  @override
  _StatusScreenState createState() => _StatusScreenState();
}

class _StatusScreenState extends State<StatusScreen> {
  final TextEditingController _controller = TextEditingController();
  bool _isLoading = false;
  bool isEnglish = true;

  String t(String en, String tl) => isEnglish ? en : tl;

  void _checkStatus() async {
    String documentId = _controller.text.trim();

    if (documentId.isEmpty) {
      _showMessage(t("Please enter a Document ID.", "Pakilagay ang Document ID."));
      return;
    }

    setState(() => _isLoading = true);

    try {
      final response = await http.get(Uri.parse(
        'http://192.168.254.100/audisure/audisure_app/audisure_api/status.php?document_uid=$documentId',
      ));

      setState(() => _isLoading = false);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        final status = data['status']?.toString().toLowerCase();

        if (status != null && ["pending", "approved", "rejected"].contains(status)) {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => StatusResultScreen(
                status: status,
              ),
            ),
          );
        } else {
          _showMessage(t("Invalid status received.", "Hindi wastong status ang natanggap."));
        }
      } else {
        _showMessage(t("Failed to load status.", "Nabigong i-load ang status."));
      }
    } catch (e) {
      setState(() => _isLoading = false);
      _showMessage(t("An error occurred: $e", "May naganap na error: $e"));
    }
  }

  void _showMessage(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          message,
          style: const TextStyle(fontSize: 16, fontFamily: 'Inter'),
        ),
        backgroundColor: const Color(0xFFD32F2F),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    const Color primaryRed = Color(0xFFD32F2F);
    const Color lightRed = Color(0xFFFFEBEE);
    const Color white = Colors.white;
    const Color darkGrey = Color(0xFF424242);
    const Color mediumGrey = Color(0xFF757575);
    const Color lightGrey = Color(0xFFEEEEEE);

    return Scaffold(
      backgroundColor: lightRed,
      appBar: AppBar(
        title: Text(
          t("Document Status", "Status ng Dokumento"),
          style: const TextStyle(
            fontFamily: 'Inter',
            fontWeight: FontWeight.w600,
            fontSize: 20,
            color: white,
          ),
        ),
        backgroundColor: primaryRed,
        centerTitle: true,
        elevation: 0,
        shape: const ContinuousRectangleBorder(
          borderRadius: BorderRadius.only(
            bottomLeft: Radius.circular(24),
            bottomRight: Radius.circular(24),
          ),
        ),
        toolbarHeight: 80,
      ),
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 24),
                child: Column(
                  children: [
                    // Header Card
                    Card(
                      color: white,
                      elevation: 2,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(20),
                        child: Column(
                          children: [
                            const Icon(Icons.assignment_outlined, 
                                size: 40, 
                                color: Color(0xFFD32F2F)),
                            const SizedBox(height: 16),
                            Text(
                              t("Check Document Status", 
                                 "Tingnan ang Status ng Dokumento"),
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 18,
                                fontWeight: FontWeight.w600,
                                color: darkGrey,
                              ),
                              textAlign: TextAlign.center,
                            ),
                            const SizedBox(height: 8),
                            Text(
                              t("Enter your document ID below", 
                                 "Ilagay ang iyong document ID sa ibaba"),
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 14,
                                color: mediumGrey,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),
                    
                    // Input Card
                    Card(
                      color: white,
                      elevation: 2,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(20),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              t("Document ID", "ID ng Dokumento"),
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 14,
                                fontWeight: FontWeight.w500,
                                color: darkGrey,
                              ),
                            ),
                            const SizedBox(height: 8),
                            TextField(
                              controller: _controller,
                              style: const TextStyle(
                                fontSize: 16, 
                                fontFamily: 'Inter',
                                color: darkGrey,
                              ),
                              decoration: InputDecoration(
                                filled: true,
                                fillColor: lightGrey,
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(8),
                                  borderSide: BorderSide.none,
                                ),
                                contentPadding: const EdgeInsets.symmetric(
                                    horizontal: 16, vertical: 14),
                                hintText: t("Enter document ID", 
                                           "Ilagay ang ID ng dokumento"),
                                hintStyle: const TextStyle(color: mediumGrey),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),
                    
                    // Check Status Button
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _isLoading ? null : _checkStatus,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: primaryRed,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        child: _isLoading
                            ? const SizedBox(
                                width: 20,
                                height: 20,
                                child: CircularProgressIndicator(
                                  color: white,
                                  strokeWidth: 2,
                                ),
                              )
                            : Text(
                                t("Check Status", "Tingnan ang Status"),
                                style: const TextStyle(
                                  fontFamily: 'Inter',
                                  fontWeight: FontWeight.w600,
                                  fontSize: 16,
                                  color: white,
                                ),
                              ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            
            // Language Toggle Bar
            Container(
              padding: const EdgeInsets.symmetric(vertical: 16),
              decoration: BoxDecoration(
                color: white,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.1),
                    blurRadius: 10,
                    offset: const Offset(0, -5),
                  ),
                ],
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  GestureDetector(
                    onTap: () {
                      if (!isEnglish) {
                        setState(() {
                          isEnglish = true;
                        });
                      }
                    },
                    child: Text(
                      'English',
                      style: TextStyle(
                        fontFamily: 'Inter',
                        color: isEnglish ? primaryRed : mediumGrey,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    child: Container(
                      height: 20,
                      width: 1,
                      color: lightGrey,
                    ),
                  ),
                  GestureDetector(
                    onTap: () {
                      if (isEnglish) {
                        setState(() {
                          isEnglish = false;
                        });
                      }
                    },
                    child: Text(
                      'Tagalog',
                      style: TextStyle(
                        fontFamily: 'Inter',
                        color: !isEnglish ? primaryRed : mediumGrey,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
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