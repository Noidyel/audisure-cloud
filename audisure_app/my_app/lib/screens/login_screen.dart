import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'status_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  bool isLoading = false;
  bool isEnglish = true;

  String t(String en, String tl) => isEnglish ? en : tl;

  Future<void> _login() async {
    final email = emailController.text.trim();
    final password = passwordController.text.trim();

    if (email.isEmpty || password.isEmpty) {
      _showMessage(t(
          "Please enter your email and password",
          "Paki-type ang iyong email at password"));
      return;
    }

    setState(() => isLoading = true);

    final url = Uri.parse('http://192.168.254.100/audisure/audisure_app/audisure_api/login.php');
    final response = await http.post(
      url,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );

    setState(() => isLoading = false);

    final data = jsonDecode(response.body);
    if (data['status'] == 'success') {
      _showMessage(t("Login successful", "Matagumpay ang pag-login"));
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const StatusScreen()),
      );
    } else {
      _showMessage(data['message'] ?? t("Login failed", "Bigo ang pag-login"));
    }
  }

  void _showMessage(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          message,
          style: const TextStyle(fontFamily: 'Inter', fontSize: 16),
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
          t("Applicant Login", "Pag-login ng Aplikante"),
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
                padding: const EdgeInsets.all(24),
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
                            const Icon(Icons.account_circle_outlined,
                                size: 48,
                                color: Color(0xFFD32F2F)),
                            const SizedBox(height: 16),
                            Text(
                              t("Welcome Back", "Maligayang Pagbabalik"),
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 18,
                                fontWeight: FontWeight.w600,
                                color: darkGrey,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              t("Log in to check your document status",
                                "Mag-login para makita ang status ng dokumento"),
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
                    const SizedBox(height: 32),

                    // Login Form Card
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
                              "Email",
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 14,
                                fontWeight: FontWeight.w500,
                                color: darkGrey,
                              ),
                            ),
                            const SizedBox(height: 8),
                            TextField(
                              controller: emailController,
                              keyboardType: TextInputType.emailAddress,
                              style: const TextStyle(
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
                                hintText: "your.email@example.com",
                                hintStyle: const TextStyle(color: mediumGrey),
                              ),
                            ),
                            const SizedBox(height: 20),
                            Text(
                              t("Password", "Password"),
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 14,
                                fontWeight: FontWeight.w500,
                                color: darkGrey,
                              ),
                            ),
                            const SizedBox(height: 8),
                            TextField(
                              controller: passwordController,
                              obscureText: true,
                              style: const TextStyle(
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
                                hintText: t("Enter password", "Ilagay ang password"),
                                hintStyle: const TextStyle(color: mediumGrey),
                              ),
                            ),
                            const SizedBox(height: 24),
                            SizedBox(
                              width: double.infinity,
                              child: ElevatedButton(
                                onPressed: isLoading ? null : _login,
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: primaryRed,
                                  padding: const EdgeInsets.symmetric(vertical: 16),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                ),
                                child: isLoading
                                    ? const SizedBox(
                                        width: 20,
                                        height: 20,
                                        child: CircularProgressIndicator(
                                          color: white,
                                          strokeWidth: 2,
                                        ),
                                      )
                                    : Text(
                                        t("Login", "Mag-login"),
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
                    const SizedBox(height: 16),
                    TextButton(
                      onPressed: () => Navigator.pushNamed(context, '/register'),
                      child: Text(
                        t("Don't have an account? Register here",
                            "Wala ka pang account? Magparehistro dito"),
                        style: const TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 14,
                          color: primaryRed,
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