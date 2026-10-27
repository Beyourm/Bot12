<?php
// إعداد قاعدة البيانات التعليمية بأسلوب آمن ومستقر
$db_file = __DIR__ . "/data/edu_platform.db";
$db = null;

try {
    $data_dir = dirname($db_file);

    if (!is_dir($data_dir) &&
        !mkdir($data_dir, 0755, true) &&
        !is_dir($data_dir)) {
        throw new RuntimeException("تعذر إنشاء مجلد البيانات");
    }

    $db = new PDO("sqlite:" . $db_file, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $db->exec("PRAGMA foreign_keys = ON");
    $db->exec("PRAGMA busy_timeout = 5000");

    $db->exec("
        CREATE TABLE IF NOT EXISTS courses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL DEFAULT '',
            category TEXT NOT NULL DEFAULT 'عام',
            price REAL NOT NULL DEFAULT 0 CHECK(price >= 0),
            created_at INTEGER NOT NULL
        )
    ");

    $db->exec("
        CREATE TABLE IF NOT EXISTS lessons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            course_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            content TEXT NOT NULL DEFAULT '',
            file_id TEXT,
            sort_order INTEGER NOT NULL DEFAULT 0,
            FOREIGN KEY(course_id)
                REFERENCES courses(id) ON DELETE CASCADE
        )
    ");

    $db->exec("
        CREATE TABLE IF NOT EXISTS student_progress (
            student_id TEXT NOT NULL,
            course_id INTEGER NOT NULL,
            completed_lessons TEXT NOT NULL DEFAULT '',
            enrolled_at INTEGER NOT NULL,
            UNIQUE(student_id, course_id),
            FOREIGN KEY(course_id)
                REFERENCES courses(id) ON DELETE CASCADE
        )
    ");

} catch (Throwable $e) {
    error_log("EDU_DATABASE_ERROR: " . $e->getMessage());
    http_response_code(500);
    exit("Database initialization failed");
}

// نظام حالات المشرفين المخصص لكل مستخدم لمنع التداخل (مع التحقق من وجود المتغيرات العامة للمصنع)
$user_amr_file = isset($from_id) ? "sudo/amr_" . $from_id . ".txt" : "";
$amr = ($user_amr_file && file_exists($user_amr_file)) ? trim(file_get_contents($user_amr_file)) : "null";

function set_user_amr($uid,$state) {
    if ($uid) {
        @mkdir("sudo", 0755, true);
        file_put_contents("sudo/amr_" . $uid . ".txt", $state);
    }
}

// --- القائمة الرئيسية (Start) ---
if(isset($text) && $text == "/start" && isset($from_id)){
    set_user_amr($from_id, "null");
    $keyboard = [
        'inline_keyboard' => [
            [['text' => '📚 استعراض الدورات والدروس', 'callback_data' => 'list_courses']],
            [['text' => '👤 حسابي والتقدم', 'callback_data' => 'my_profile'], ['text' => 'ℹ️ عن المنصة', 'callback_data' => 'about_platform']],
            [['text' => '📞 تواصل مع الدعم الفني', 'callback_data' => 'support']]
        ]
    ];
    
    if(isset($sudo) && is_array($sudo) && in_array($from_id, $sudo)){$keyboard['inline_keyboard'][] = [['text' => '⚙️ لوحة تحكم المنصة التعليمية', 'callback_data' => 'edu_admin_panel']];
    }
    
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "مرحباً بك عزيزي الطالب [$name](tg://user?id=$chat_id) في المنصة التعليمية الذكية 🎓\n\nيمكنك من خلال هذا البوت متابعة الدورات، الدروس، وتطوير مهاراتك بكل سهولة.",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($keyboard)
    ]);
    return;
}

if(isset($data) && $data == 'main_menu' && isset($from_id)){
    set_user_amr($from_id, "null");
    $keyboard = [
        'inline_keyboard' => [
            [['text' => '📚 استعراض الدورات والدروس', 'callback_data' => 'list_courses']],
            [['text' => '👤 حسابي والتقدم', 'callback_data' => 'my_profile'], ['text' => 'ℹ️ عن المنصة', 'callback_data' => 'about_platform']],
            [['text' => '📞 تواصل مع الدعم الفني', 'callback_data' => 'support']]
        ]
    ];
    if(isset($sudo) && is_array($sudo) && in_array($from_id, $sudo)){$keyboard['inline_keyboard'][] = [['text' => '⚙️ لوحة تحكم المنصة التعليمية', 'callback_data' => 'edu_admin_panel']];
    }
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "مرحباً بك مرة أخرى في القائمة الرئيسية للمنصة التعليمية 🎓",
        'reply_markup' => json_encode($keyboard)
    ]);
}

// --- استعراض الدورات التعليمية ---
if(isset($data) &&$data == 'list_courses'){
    $stmt =$db->query("SELECT * FROM courses ORDER BY id DESC");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);$inline = [];
    if(!empty($courses)){
        foreach($courses as $c){$inline[] = [['text' => '📖 ' . $c['title'], 'callback_data' => 'show_course_' .$c['id']]];
        }
    } else {
        $inline[] = [['text' => '❌ لا توجد دورات متاحة حالياً', 'callback_data' => 'null']];
    }
    $inline[] = [['text' => '🔙 رجوع للقائمة الرئيسية', 'callback_data' => 'main_menu']];
    
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "📚 اختر الدورة التعليمية التي تود دراستها:",
        'reply_markup' => json_encode(['inline_keyboard' => $inline])
    ]);
}

// --- عرض تفاصيل الدورة ---
if(isset($data) && strpos($data, 'show_course_') === 0){
    $course_id = str_replace('show_course_', '',$data);
    $stmt =$db->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $c =$stmt->fetch(PDO::FETCH_ASSOC);
    
    if($c){$text_msg = "📖 **" . $c['title'] . "**\n\n" . $c['description'] . "\n\n📌 اختر ما تود القيام به:";
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '⭐ تسجيل في الدورة', 'callback_data' => 'enroll_' . $course_id]],
                [['text' => '🔙 عودة لقائمة الدورات', 'callback_data' => 'list_courses']]
            ]
        ];
        
        bot('editMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $text_msg,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode($keyboard)
        ]);
    }
}

// --- تسجيل الطالب في الدورة ---
if(isset($data) && strpos($data, 'enroll_') === 0 && isset($from_id)){
    $course_id = str_replace('enroll_', '',$data);
    $stmt =$db->prepare("SELECT * FROM student_progress WHERE student_id = ? AND course_id = ?");
    $stmt->execute([$from_id,$course_id]);
    $check =$stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$check){
        $ins =$db->prepare("INSERT INTO student_progress (student_id, course_id, completed_lessons, enrolled_at) VALUES (?, ?, ?, ?)");
        $ins->execute([$from_id,$course_id, '', time()]);
        
        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => '✅ تم تسجيلك في الدورة بنجاح!',
            'show_alert' => true
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => '⚠️ أنت مسجل في هذه الدورة مسبقاً.',
            'show_alert' => true
        ]);
    }
}

// --- ملف الطالب والتقدم ---
if(isset($data) && $data == 'my_profile' && isset($from_id)){
    $stmt =$db->prepare("SELECT COUNT(*) as total FROM student_progress WHERE student_id = ?");
    $stmt->execute([$from_id]);
    $res =$stmt->fetch(PDO::FETCH_ASSOC);
    $enrolled_count =$res['total'] ?? 0;
    
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "👤 **ملفك الشخصي والتقدم**\n\n• الاسم: $name\n• الأيدي: `$from_id`\n• الدورات المسجلة: $enrolled_count\n• مستوى النشاط: 🌟 متفاعل نشط",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '🔙 رجوع', 'callback_data' => 'main_menu']]]])
    ]);
}

// --- عن المنصة والدعم ---
if(isset($data) &&$data == 'about_platform'){
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "ℹ️ **عن المنصة التعليمية**\n\nمنصة متكاملة ومتقدمة لتقديم الدورات التدريبية والشروحات الاحترافية عبر تيليجرام.",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '🔙 رجوع', 'callback_data' => 'main_menu']]]])
    ]);
}

if(isset($data) &&$data == 'support'){
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "📞 للدعم الفني والاستفسارات، يرجى مراسلة إدارة المنصة مباشرة.",
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '🔙 رجوع', 'callback_data' => 'main_menu']]]])
    ]);
}

// ==========================================
// لوحة تحكم المنصة (خاصة بالمشرفين/المطورين)
// ==========================================







// ==========================================
// لوحة تحكم المنصة المتقدمة (خاصة بالمشرفين/المطورين)
// ==========================================
if(isset($data) && $data == 'edu_admin_panel' && isset($from_id) && isset($sudo) && in_array($from_id, $sudo)){
    set_user_amr($from_id, "null");
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "⚙️ **لوحة تحكم المنصة التعليمية الشاملة**\n\nاختر القسم الإداري المطلوب للتحكم الكامل:",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(['inline_keyboard' => [
            [['text' => '📚 إدارة الدورات', 'callback_data' => 'admin_courses'], ['text' => '👥 إدارة المتدربين', 'callback_data' => 'admin_students']],
            [['text' => '🛡️ إدارة المشرفين', 'callback_data' => 'admin_admins'], ['text' => '🏢 إدارة الفروع', 'callback_data' => 'admin_branches']],
            [['text' => '💵 العملات والأسعار', 'callback_data' => 'admin_currencies'], ['text' => '🎁 العروض والخصومات', 'callback_data' => 'admin_offers']],
            [['text' => '🎟️ أكواد الخصم', 'callback_data' => 'admin_coupons'], ['text' => '📂 مكتبة الملفات', 'callback_data' => 'admin_library']],
            [['text' => '🔙 رجوع للقائمة الرئيسية', 'callback_data' => 'main_menu']]
        ]])
    ]);
}

// --- معالجات الأزرار الجديدة في لوحة التحكم (هياكل واجهات الأقسام) ---
if(isset($data) && in_array($data, ['admin_courses', 'admin_students', 'admin_admins', 'admin_branches', 'admin_currencies', 'admin_offers', 'admin_coupons', 'admin_library']) && isset($from_id) && isset($sudo) && in_array($from_id, $sudo)){
    
    $titles = [
        'admin_courses' => "📚 **إدارة الدورات والدروس**\n\nاختر الإجراء المناسب:",
        'admin_students' => "👥 **إدارة المتدربين**\n\nعرض بيانات الطلاب وتتبع تقدمهم:",
        'admin_admins' => "🛡️ **إدارة المشرفين والصلاحيات**\n\nإضافة أو حذف مشرفين وتحديد صلاحياتهم:",
        'admin_branches' => "🏢 **إدارة الفروع**\n\nإضافة وتعديل فروع المنصة:",
        'admin_currencies' => "💵 **إدارة العملات**\n\nتحديد العملات وأسعار الصرف:",
        'admin_offers' => "🎁 **إدارة العروض**\n\nإضافة عروض وتخفيضات جديدة:",
        'admin_coupons' => "🎟️ **أكواد الخصم**\n\nإنشاء وإدارة قسائم وكوبونات الخصم:",
        'admin_library' => "📂 **مكتبة الملفات والمصادر**\n\nإدارة الملفات المرفقة والكتب:"
    ];

    $inline_keyboard = [];
    
    // أزرار تفاعلية لكل قسم (إضافة وعرض)
    if($data == 'admin_courses'){
        $inline_keyboard[] = [['text' => '➕ إضافة دورة جديدة', 'callback_data' => 'add_course']];
    } elseif($data == 'admin_admins'){
        $inline_keyboard[] = [['text' => '➕ إضافة مشرف جديد', 'callback_data' => 'add_admin_step']];
    } elseif($data == 'admin_branches'){
        $inline_keyboard[] = [['text' => '➕ إضافة فرع جديد', 'callback_data' => 'add_branch_step']];
    } elseif($data == 'admin_currencies'){
        $inline_keyboard[] = [['text' => '➕ إضافة عملة جديدة', 'callback_data' => 'add_currency_step']];
    } elseif($data == 'admin_offers'){
        $inline_keyboard[] = [['text' => '➕ إضافة عرض جديد', 'callback_data' => 'add_offer_step']];
    } elseif($data == 'admin_coupons'){
        $inline_keyboard[] = [['text' => '➕ إضافة كود خصم', 'callback_data' => 'add_coupon_step']];
    } elseif($data == 'admin_library'){
        $inline_keyboard[] = [['text' => '➕ رفع ملف للمكتبة', 'callback_data' => 'add_library_step']];
    } elseif($data == 'admin_students'){
        $inline_keyboard[] = [['text' => '📊 تقرير عام للمتدربين', 'callback_data' => 'students_report']];
    }

    $inline_keyboard[] = [['text' => '🔙 عودة لوحة التحكم', 'callback_data' => 'edu_admin_panel']];

    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $titles[$data],
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
    ]);
}

// --- مثال عملي: زر إضافة دورة جديدة (موجود مسبقاً ومربوط بالنظام) ---
if(isset($data) && $data == 'add_course' && isset($from_id) && isset($sudo) && in_array($from_id, $sudo)){
    set_user_amr($from_id, "add_course_title");
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "📝 أرسل الآن عنوان الدورة التعليمية الجديدة:",
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '❌ إلغاء', 'callback_data' => 'edu_admin_panel']]]])
    ]);
}


// ==========================================
// كتلة إضافة دورة 
// ==========================================

if(isset($data) &&$data == 'add_course' && isset($from_id) && isset($sudo) && in_array($from_id,$sudo)){
    set_user_amr($from_id, "add_course_title");
    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "📝 أرسل الآن عنوان الدورة التعليمية الجديدة:",
        'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '❌ إلغاء', 'callback_data' => 'edu_admin_panel']]]])
    ]);
}

// استقبال الردود الخطية للمشرف عبر النظام الآمن للحالات
if(isset($text) && isset($from_id) && isset($sudo) && in_array($from_id,$sudo)){
    if($amr == 'add_course_title'){
        file_put_contents("sudo/temp_title_" . $from_id . ".txt", $text);
        set_user_amr($from_id, "add_course_desc");
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "📄 حسنتاً، أرسل الآن وصف الدورة التعليمية:",
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '❌ إلغاء', 'callback_data' => 'edu_admin_panel']]]])
        ]);
        return;
    }
    
    if($amr == 'add_course_desc'){
        $title_file = "sudo/temp_title_" . $from_id . ".txt";
        $title = file_exists($title_file) ? file_get_contents($title_file) : "دورة تدريبية";
        $desc =$text;
        
        $stmt =$db->prepare("INSERT INTO courses (title, description, created_at) VALUES (?, ?, ?)");
        $stmt->execute([$title,$desc, time()]);
        
        set_user_amr($from_id, "null");
        @unlink($title_file);
        
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "✅ تم إضافة الدورة التعليمية بنجاح!\n\n📌 العنوان: $title",
            'reply_markup' => json_encode(['inline_keyboard' => [[['text' => '⚙️ لوحة التحكم', 'callback_data' => 'edu_admin_panel']]]])
        ]);
        return;
    }
}



// ==========================================
// لوحة تحكم المنصة (خاصة بالمشرفين/المطورين)
// ==========================================




// ==========================================
// لوحة تحكم  العقود 
// ==========================================





// ==========================================
// لوحة تحكم الفروع والعملات 
// ==========================================







// ==========================================
// لوحة تحكم اكواد الخصم والعروض
// ==========================================

?>
