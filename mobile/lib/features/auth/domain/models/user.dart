class User {
  final String id;
  final String name;
  final String email;
  final String companyName;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.companyName,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      companyName: json['company_name'] ?? 'Tony Stark Ltd',
    );
  }
}
