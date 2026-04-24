<?php
// ===== سكريبت إعادة الكلمات لـ "نص عادي" مؤقت =====
$conn = new mysqli("localhost", "root", "", "sweet");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

echo "<h2>🔄 إصلاح كلمات المرور وتوحيدها (لأن التشفير أُلغي)</h2>";

// 1. نحن ألغينا التشفير، لكن الكلمات المشفرة في قاعدة البيانات أصبحت بلا فائدة
// 2. سنقوم بتصفير كل كلمات المرور إلى نص عادي مثل "123456" 
// بحيث يستطيعทุกคน الدخول، ثم يغيرها بمعرفته أو تغيرها أنت من لوحة التحكم

$default_pass = "123456";

// تحديث جدول users
$q1 = $conn->query("UPDATE users SET password = '$default_pass'");
if ($q1) {
    echo "<p>✅ تم تصفير جميع كلمات مرور العملاء (users) إلى: <b>$default_pass</b></p>";
} else {
    echo "<p>❌ فشل تحديث users: " . $conn->error . "</p>";
}

// تحديث جدول staff
$q2 = $conn->query("UPDATE staff SET password = '$default_pass'");
if ($q2) {
    echo "<p>✅ تم تصفير جميع كلمات مرور الإدارة والموظفين (staff) إلى: <b>$default_pass</b></p>";
} else {
    echo "<p>❌ فشل تحديث staff: " . $conn->error . "</p>";
}

echo "<hr><p>الآن جرب الدخول بأي حساب، باستخدام كلمة المرور: <b>$default_pass</b></p>";

$conn->close();
?>
