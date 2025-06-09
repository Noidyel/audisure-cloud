import 'package:flutter/material.dart';
import '/screens/login_screen.dart';
import '/screens/register_screen.dart';
import '/screens/status_screen.dart'; // You can rename `MyForm` to `StatusScreen`

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Audisure App',
      debugShowCheckedModeBanner: false,
      initialRoute: '/register', // or '/login' if you prefer starting with login
      routes: {
        '/login': (context) => const LoginScreen(),
        '/register': (context) => const RegisterScreen(),
        '/status': (context) => const StatusScreen(),
      },
    );
  }
}
