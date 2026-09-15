<?php

/**
 * الخطة الدراسية النهائية 8-2026
 * Source: الخطة الدراسية الجديدة 8-2026.docx
 */
return [
    'intro_ar' => <<<'TEXT'
يهدف المعهد إلى تخريج جيل من الدعاة الواعين وطلاب العلم النابهين، وتأصيل الطلاب علميًا وتأهيلهم ليكونوا من العلماء العاملين، القادرين على فهم الكتاب والسنة، والوقوف على كلام أهل العلم، والدعوة إلى الله تعالى بالحكمة والبصيرة.

تقوم الدراسة على التدرج العلمي من التأسيس إلى التأصيل ثم التخصص. مدة الدراسة خمس سنوات: السنة التمهيدية (سنة عامة)، ثم مستوى التأصيل العلمي (سنتان)، ثم مستوى التخصص (سنتان) في إحدى الشعب الأربع.

نظام التقييم: الاختبارات التحريرية 70%، الحضور والمشاركة والواجبات 15%، البحوث والتطبيقات العلمية 15%.
TEXT,

    'levels' => [
        1 => [
            'name_ar' => 'السنة التمهيدية',
            'slug' => 'preparatory-level',
            'description_ar' => 'سنة عامة لجميع الطلاب — إطلاع على مبادئ العلوم الشرعية واللغوية.',
        ],
        2 => [
            'name_ar' => 'مستوى التأصيل العلمي',
            'slug' => 'taaseel-level',
            'description_ar' => 'سنتان — توسيع وتأصيل في العلوم الشرعية.',
        ],
        3 => [
            'name_ar' => 'مستوى التخصص',
            'slug' => 'specialized-level',
            'description_ar' => 'سنتان — التخصص في إحدى الشعب الأربع.',
        ],
    ],

    'specializations' => [
        ['slug' => 'aqeedah-sects', 'name_ar' => 'شعبة العقيدة والفرق', 'name_en' => 'Aqeedah & Sects', 'sort' => 1],
        ['slug' => 'tafsir-sciences', 'name_ar' => 'شعبة التفسير وعلوم القرآن', 'name_en' => 'Tafsir & Quranic Sciences', 'sort' => 2],
        ['slug' => 'fiqh-usul', 'name_ar' => 'شعبة الفقه وأصول الفقه', 'name_en' => 'Fiqh & Usul al-Fiqh', 'sort' => 3],
        ['slug' => 'hadith-sciences', 'name_ar' => 'شعبة الحديث وعلومه', 'name_en' => 'Hadith & Its Sciences', 'sort' => 4],
    ],

    'legacy_specialization_slugs' => ['fiqh-tafsir', 'usool-deen-archived', 'hadith', 'aqeedah'],

    'programs' => [
        [
            'slug' => 'preparatory-program',
            'name_ar' => 'السنة التمهيدية',
            'duration' => 'سنة واحدة',
            'level' => 'تأسيس',
            'description_ar' => 'سنة عامة لجميع الطلاب — إطلاع على مبادئ العلوم الشرعية واللغوية بصورة ميسّرة مع متانة المادة العلمية.',
            'sort_order' => 1,
        ],
        [
            'slug' => 'taaseel-program',
            'name_ar' => 'مستوى التأصيل العلمي',
            'duration' => 'سنتان',
            'level' => 'تأصيل',
            'description_ar' => 'السنة الثانية والثالثة — توسيع وتأصيل في العلوم الشرعية، مع بناء الطالب علمياً للدعوة والخطابة.',
            'sort_order' => 2,
        ],
        [
            'slug' => 'specialization-program',
            'name_ar' => 'مستوى التخصص',
            'duration' => 'سنتان',
            'level' => 'تخصص',
            'description_ar' => 'السنة الرابعة والخامسة — التخصص في إحدى الشعب الأربع: العقيدة، التفسير، الفقه، أو الحديث.',
            'sort_order' => 3,
        ],
    ],

    'legacy_program_slugs' => ['intro', 'intermediate', 'advanced', 'brnamg-altasyl-alaalmy', 'brnamg-altkhss'],

    'general' => [
        'first-year' => [
            1 => [
                ['slug' => 'y1s1-tawheed', 'name_ar' => 'التوحيد', 'primary_text_ar' => 'التوحيد للمقريزي', 'supplementary_text_ar' => 'المدرس: فضيلة الشيخ عادل السيد'],
                ['slug' => 'y1s1-seerah', 'name_ar' => 'السيرة', 'primary_text_ar' => 'الفصول في سيرة الرسول ﷺ لابن كثير', 'supplementary_text_ar' => 'المدرس: أ.د. طه عبد المقصود'],
                ['slug' => 'y1s1-tafsir', 'name_ar' => 'التفسير', 'primary_text_ar' => 'تفسير قصار السور من تفسير السعدي', 'supplementary_text_ar' => 'المدرس: د/ عمرو سادات'],
                ['slug' => 'y1s1-nahw', 'name_ar' => 'النحو', 'primary_text_ar' => 'شرح الآجرومية', 'supplementary_text_ar' => 'المدرس: الشيخ/ وليد بغدادي'],
                ['slug' => 'y1s1-usul-fiqh', 'name_ar' => 'أصول الفقه', 'primary_text_ar' => 'رسالة لطيفة للسعدي', 'supplementary_text_ar' => 'المدرس: د/ محمد سميح'],
                ['slug' => 'y1s1-fiqh', 'name_ar' => 'الفقه', 'primary_text_ar' => 'مدخل للفقه الإسلامي وتاريخه + فقه الطهارة والصلاة من «منهج السالكين»', 'supplementary_text_ar' => 'المدرس: الشيخ/ تامر هادي'],
                ['slug' => 'y1s1-mustalah', 'name_ar' => 'مصطلح الحديث', 'primary_text_ar' => 'مدخل عن السنة ومكانتها وتدوينها + البيقونية', 'supplementary_text_ar' => 'المدرس: الشيخ/ عصام أبو السعود'],
                ['slug' => 'y1s1-memorization', 'name_ar' => 'مقرر الحفظ', 'memorization_ar' => 'حفظ جزء عم'],
            ],
            2 => [
                ['slug' => 'y1s2-aqeedah', 'name_ar' => 'العقيدة', 'primary_text_ar' => 'المعتقد الصحيح لعبد السلام بن برجس', 'supplementary_text_ar' => 'المدرس: فضيلة الشيخ عادل السيد'],
                ['slug' => 'y1s2-seerah', 'name_ar' => 'السيرة', 'primary_text_ar' => 'الفصول في سيرة الرسول ﷺ لابن كثير', 'supplementary_text_ar' => 'المدرس: أ.د. طه عبد المقصود'],
                ['slug' => 'y1s2-fiqh', 'name_ar' => 'الفقه', 'primary_text_ar' => 'الصيام والزكاة والحج من «منهج السالكين»', 'supplementary_text_ar' => 'المدرس: الشيخ/ تامر هادي'],
                ['slug' => 'y1s2-balagha', 'name_ar' => 'البلاغة', 'primary_text_ar' => 'دروس البلاغة لحفني ناصف', 'supplementary_text_ar' => 'المدرس: د/ عمرو سادات'],
                ['slug' => 'y1s2-hadith', 'name_ar' => 'الحديث', 'primary_text_ar' => 'شرح الأربعين النووية', 'supplementary_text_ar' => 'المدرس: الشيخ/ وليد بغدادي'],
                ['slug' => 'y1s2-usul-tafsir', 'name_ar' => 'أصول التفسير', 'primary_text_ar' => 'أصول في التفسير للعثيمين', 'supplementary_text_ar' => 'المدرس: د/ محمد سميح'],
                ['slug' => 'y1s2-adab', 'name_ar' => 'آداب طلب العلم', 'primary_text_ar' => 'تذكرة السامع والمتكلم لابن جماعة', 'supplementary_text_ar' => 'المدرس: الشيخ/ عصام أبو السعود'],
                ['slug' => 'y1s2-memorization', 'name_ar' => 'مقرر الحفظ', 'memorization_ar' => 'أحاديث الأربعون النووية'],
            ],
        ],
        'second-year' => [
            1 => [
                ['slug' => 'y2s1-aqeedah', 'name_ar' => 'العقيدة', 'primary_text_ar' => 'معارج القبول'],
                ['slug' => 'y2s1-fiqh', 'name_ar' => 'الفقه', 'primary_text_ar' => 'عمدة الفقه – فقه المعاملات'],
                ['slug' => 'y2s1-usul-fiqh', 'name_ar' => 'أصول الفقه', 'primary_text_ar' => 'الأصول من علم الأصول للعثيمين'],
                ['slug' => 'y2s1-tafsir', 'name_ar' => 'التفسير', 'primary_text_ar' => 'تفسير ابن كثير – سور مختارة'],
                ['slug' => 'y2s1-usul-tafsir', 'name_ar' => 'أصول التفسير', 'primary_text_ar' => 'مقدمة في أصول التفسير لابن تيمية'],
                ['slug' => 'y2s1-hadith', 'name_ar' => 'الحديث', 'primary_text_ar' => 'عمدة الأحكام'],
                ['slug' => 'y2s1-mustalah', 'name_ar' => 'مصطلح الحديث', 'primary_text_ar' => 'الباعث الحثيث لابن كثير'],
                ['slug' => 'y2s1-nahw', 'name_ar' => 'النحو', 'primary_text_ar' => 'قطر الندى وبل الصدى'],
                ['slug' => 'y2s1-sarf', 'name_ar' => 'الصرف', 'primary_text_ar' => 'الصرف الصغير للعيوني / شذا العرف في فن الصرف'],
                ['slug' => 'y2s1-khulafa', 'name_ar' => 'سيرة الخلفاء الراشدين', 'primary_text_ar' => 'سيرة أبي بكر وعمر رضي الله عنهما'],
            ],
            2 => [
                ['slug' => 'y2s2-aqeedah', 'name_ar' => 'العقيدة', 'primary_text_ar' => 'معارج القبول'],
                ['slug' => 'y2s2-fiqh', 'name_ar' => 'الفقه', 'primary_text_ar' => 'عمدة الفقه – فقه المعاملات (استكمال)'],
                ['slug' => 'y2s2-usul-fiqh', 'name_ar' => 'أصول الفقه', 'primary_text_ar' => 'الأصول من علم الأصول للعثيمين'],
                ['slug' => 'y2s2-tafsir', 'name_ar' => 'التفسير', 'primary_text_ar' => 'تفسير ابن كثير – استكمال سور مختارة'],
                ['slug' => 'y2s2-hadith', 'name_ar' => 'الحديث', 'primary_text_ar' => 'عمدة الأحكام (استكمال)'],
                ['slug' => 'y2s2-mustalah', 'name_ar' => 'مصطلح الحديث', 'primary_text_ar' => 'الباعث الحثيث لابن كثير'],
                ['slug' => 'y2s2-nahw', 'name_ar' => 'النحو', 'primary_text_ar' => 'قطر الندى (استكمال)'],
                ['slug' => 'y2s2-sarf', 'name_ar' => 'الصرف', 'primary_text_ar' => 'الصرف الصغير للعيوني / شذا العرف (استكمال)'],
                ['slug' => 'y2s2-khulafa', 'name_ar' => 'سيرة الخلفاء الراشدين', 'primary_text_ar' => 'سيرة عثمان وعلي رضي الله عنهما'],
                ['slug' => 'y2s2-uloom-quran', 'name_ar' => 'علوم القرآن', 'primary_text_ar' => 'القواعد الحسان للسعدي'],
            ],
        ],
        'third-year' => [
            1 => [
                ['slug' => 'y3s1-aqeedah', 'name_ar' => 'العقيدة', 'primary_text_ar' => 'معارج القبول'],
                ['slug' => 'y3s1-fiqh', 'name_ar' => 'الفقه', 'primary_text_ar' => 'فقه المواريث'],
                ['slug' => 'y3s1-qawaid', 'name_ar' => 'القواعد الفقهية', 'primary_text_ar' => 'منظومة القواعد الفقهية للسعدي'],
                ['slug' => 'y3s1-tafsir', 'name_ar' => 'التفسير', 'primary_text_ar' => 'تفسير الطبري – مختارات'],
                ['slug' => 'y3s1-hadith', 'name_ar' => 'الحديث', 'primary_text_ar' => 'عمدة الأحكام (استكمال)'],
                ['slug' => 'y3s1-balagha', 'name_ar' => 'البلاغة', 'primary_text_ar' => 'البلاغة الواضحة'],
                ['slug' => 'y3s1-khulafa', 'name_ar' => 'سيرة الخلفاء الراشدين', 'primary_text_ar' => 'الخلافة الراشدة والفتوحات وأهم الأحداث'],
                ['slug' => 'y3s1-dawa', 'name_ar' => 'فن الدعوة والخطابة', 'primary_text_ar' => 'أصول الدعوة ومهارات الخطابة'],
                ['slug' => 'y3s1-siyasa', 'name_ar' => 'السياسة الشرعية', 'primary_text_ar' => 'معاملة الحكام في ضوء الكتاب والسنة لابن برجس'],
                ['slug' => 'y3s1-madhahib', 'name_ar' => 'المذاهب والأفكار المعاصرة', 'primary_text_ar' => 'مدخل إلى المذاهب والأفكار المعاصرة'],
                ['slug' => 'y3s1-uloom-quran', 'name_ar' => 'علوم القرآن', 'primary_text_ar' => 'مباحث في علوم القرآن لمساعد الطيار (مختارات) / المقدمات الأساسية في علوم القرآن'],
            ],
            2 => [
                ['slug' => 'y3s2-aqeedah', 'name_ar' => 'العقيدة', 'primary_text_ar' => 'معارج القبول (استكمال)'],
                ['slug' => 'y3s2-fiqh', 'name_ar' => 'الفقه', 'primary_text_ar' => 'فقه الأسرة'],
                ['slug' => 'y3s2-hadith', 'name_ar' => 'الحديث', 'primary_text_ar' => 'صحيح البخاري – أبواب وأحاديث مختارة'],
                ['slug' => 'y3s2-nahw', 'name_ar' => 'النحو', 'primary_text_ar' => 'قطر الندى (استكمال)'],
                ['slug' => 'y3s2-balagha', 'name_ar' => 'البلاغة', 'primary_text_ar' => 'البلاغة الواضحة'],
                ['slug' => 'y3s2-khulafa', 'name_ar' => 'سيرة الخلفاء الراشدين', 'primary_text_ar' => 'دراسة تحليلية لمنهج الخلفاء في الحكم والدعوة والقضاء والسياسة الشرعية'],
                ['slug' => 'y3s2-dawa', 'name_ar' => 'فن الدعوة والخطابة', 'primary_text_ar' => 'إعداد الخطب والدروس والمحاضرات'],
                ['slug' => 'y3s2-siyasa', 'name_ar' => 'السياسة الشرعية', 'primary_text_ar' => 'السياسة الشرعية لابن تيمية'],
                ['slug' => 'y3s2-madhahib', 'name_ar' => 'المذاهب والأفكار المعاصرة', 'primary_text_ar' => 'العلمانية والإلحاد والتكفير والتشيع وغيرها'],
                ['slug' => 'y3s2-waiy', 'name_ar' => 'الوعي الفكري والثقافي', 'primary_text_ar' => "الشبهات الفكرية المعاصرة وطرق التعامل معها\nمخططات الأعداء للأمة الإسلامية"],
                ['slug' => 'y3s2-maqasid', 'name_ar' => 'المقاصد الشرعية', 'primary_text_ar' => 'مقاصد الشريعة الإسلامية للعلامة محمد الطاهر بن عاشور'],
            ],
        ],
    ],

    'specialized' => [
        'aqeedah-sects' => [
            'fourth-year' => [
                1 => [
                    ['slug' => 'aq-y4s1-tawheed', 'name_ar' => 'التوحيد', 'primary_text_ar' => 'كتاب التوحيد أو دعوة التوحيد'],
                    ['slug' => 'aq-y4s1-aqeedah', 'name_ar' => 'العقيدة', 'primary_text_ar' => 'التدمرية'],
                    ['slug' => 'aq-y4s1-usool-sunnah', 'name_ar' => 'أصول أهل السنة والجماعة', 'primary_text_ar' => 'دراسة مقارنة'],
                    ['slug' => 'aq-y4s1-iman-takfir', 'name_ar' => 'الإيمان وضوابط التكفير', 'primary_text_ar' => 'ضوابط التكفير عند أهل السنة والجماعة'],
                    ['slug' => 'aq-y4s1-istidlal', 'name_ar' => 'مناهج الاستدلال العقدي', 'primary_text_ar' => 'منهج الاستدلال العقدي بين أهل السنة وأهل البدع'],
                    ['slug' => 'aq-y4s1-muqarana', 'name_ar' => 'مقارنة أديان', 'primary_text_ar' => 'محاضرات في النصرانية لأبو زهرة'],
                    ['slug' => 'aq-y4s1-munazara', 'name_ar' => 'المناظرة والحوار', 'primary_text_ar' => 'أصول المناظرة وآدابها'],
                    ['slug' => 'aq-y4s1-shubuhat', 'name_ar' => 'دراسة تطبيقية', 'primary_text_ar' => 'شبهات عقدية مختارة'],
                ],
                2 => [
                    ['slug' => 'aq-y4s2-aqeedah', 'name_ar' => 'العقيدة', 'primary_text_ar' => 'التدمرية (استكمال)'],
                    ['slug' => 'aq-y4s2-ashrat', 'name_ar' => 'أشراط الساعة', 'primary_text_ar' => 'أشراط الساعة ليوسف الوابل'],
                    ['slug' => 'aq-y4s2-sahabah', 'name_ar' => 'الصحابة', 'primary_text_ar' => 'العواصم من القواصم لابن العربي'],
                    ['slug' => 'aq-y4s2-tawheed', 'name_ar' => 'التوحيد', 'primary_text_ar' => 'كتاب التوحيد أو دعوة التوحيد'],
                    ['slug' => 'aq-y4s2-firaq', 'name_ar' => 'الفرق الإسلامية', 'primary_text_ar' => 'الموسوعة الميسرة — دراسة تحليلية للفرق المنتسبة للإسلام'],
                    ['slug' => 'aq-y4s2-tashayyu', 'name_ar' => 'التشيع المعاصر', 'primary_text_ar' => 'دراسة أصوله وشبهاته'],
                    ['slug' => 'aq-y4s2-khawarij', 'name_ar' => 'الخوارج والغلو', 'primary_text_ar' => 'التكفير والغلو'],
                    ['slug' => 'aq-y4s2-ilhad', 'name_ar' => 'الإلحاد المعاصر', 'primary_text_ar' => 'الأصول والشبهات'],
                    ['slug' => 'aq-y4s2-ilmaniya', 'name_ar' => 'العلمانية', 'primary_text_ar' => 'النشأة والأصول'],
                    ['slug' => 'aq-y4s2-takfir', 'name_ar' => 'التكفير', 'primary_text_ar' => 'ضوابطه ومزالقه'],
                    ['slug' => 'aq-y4s2-dialogue', 'name_ar' => 'الحوار والمناظرة', 'primary_text_ar' => 'تطبيقات عملية'],
                ],
            ],
            'fifth-year' => [
                1 => [
                    ['slug' => 'aq-y5s1-usool-sunnah', 'name_ar' => 'أصول أهل السنة والجماعة', 'primary_text_ar' => 'دراسة مقارنة'],
                    ['slug' => 'aq-y5s1-shia', 'name_ar' => 'الشيعة ومذاهبها المعاصرة', 'primary_text_ar' => 'دراسة تحليلية'],
                    ['slug' => 'aq-y5s1-khawarij', 'name_ar' => 'الخوارج والغلو والتكفير', 'primary_text_ar' => 'دراسة متقدمة'],
                    ['slug' => 'aq-y5s1-mutazila', 'name_ar' => 'المعتزلة والجهمية والمرجئة', 'primary_text_ar' => 'دراسة تحليلية'],
                    ['slug' => 'aq-y5s1-tasawwuf', 'name_ar' => 'التصوف والغلو', 'primary_text_ar' => 'دراسة نقدية'],
                    ['slug' => 'aq-y5s1-ilhad', 'name_ar' => 'الإلحاد المعاصر', 'primary_text_ar' => 'مناقشة الشبهات'],
                    ['slug' => 'aq-y5s1-liberalism', 'name_ar' => 'العلمانية والليبرالية', 'primary_text_ar' => 'دراسة مقارنة'],
                    ['slug' => 'aq-y5s1-naqd', 'name_ar' => 'مناهج نقد الأفكار', 'primary_text_ar' => 'تطبيقات في الرد على الشبهات'],
                ],
                2 => [
                    ['slug' => 'aq-y5s2-advanced-firaq', 'name_ar' => 'دراسات متقدمة في الفرق والمذاهب', 'primary_text_ar' => 'دراسات تطبيقية'],
                    ['slug' => 'aq-y5s2-contemporary', 'name_ar' => 'شبهات معاصرة حول العقيدة', 'primary_text_ar' => 'مناقشة وتحليل'],
                    ['slug' => 'aq-y5s2-ilhad-reply', 'name_ar' => 'مناقشة شبهات الإلحاد', 'primary_text_ar' => 'تطبيقات'],
                    ['slug' => 'aq-y5s2-sunnah-doubt', 'name_ar' => 'شبهات الطاعنين في السنة', 'primary_text_ar' => 'ذات الصلة بالعقيدة'],
                    ['slug' => 'aq-y5s2-takfir-ghulu', 'name_ar' => 'قضايا التكفير والغلو', 'primary_text_ar' => 'دراسة تطبيقية'],
                    ['slug' => 'aq-y5s2-debate', 'name_ar' => 'التدريب المتقدم على المناظرة', 'primary_text_ar' => 'حلقات بحث ومناقشة'],
                    ['slug' => 'aq-y5s2-thesis', 'name_ar' => 'البحث التخصصي', 'primary_text_ar' => 'إعداد بحث تخصصي ومناقشته'],
                ],
            ],
        ],
        'tafsir-sciences' => [
            'fourth-year' => [
                1 => [
                    ['slug' => 'tf-y4s1-uloom', 'name_ar' => 'علوم القرآن', 'primary_text_ar' => 'دراسات في علوم القرآن للرومي'],
                    ['slug' => 'tf-y4s1-qawaid', 'name_ar' => 'قواعد التفسير وأصوله', 'primary_text_ar' => 'قواعد التفسير لخالد السبت'],
                    ['slug' => 'tf-y4s1-tatbiq', 'name_ar' => 'تطبيقات تفسيرية', 'primary_text_ar' => 'كتاب النبأ العظيم'],
                    ['slug' => 'tf-y4s1-tarikh', 'name_ar' => 'تاريخ التفسير', 'primary_text_ar' => 'دراسة تاريخية'],
                    ['slug' => 'tf-y4s1-shubuhat', 'name_ar' => 'شبهات حول القرآن', 'primary_text_ar' => 'دراسة ورد'],
                    ['slug' => 'tf-y4s1-qiraat', 'name_ar' => 'القراءات', 'primary_text_ar' => 'أثرها في التفسير'],
                    ['slug' => 'tf-y4s1-manahij', 'name_ar' => 'مناهج المفسرين', 'primary_text_ar' => 'التفسير بالمأثور والتفسير بالرأي'],
                ],
                2 => [
                    ['slug' => 'tf-y4s2-uloom', 'name_ar' => 'علوم القرآن', 'primary_text_ar' => 'المقدمات الأساسية في علوم القرآن (استكمال)'],
                    ['slug' => 'tf-y4s2-manahij', 'name_ar' => 'مناهج التفسير', 'primary_text_ar' => 'التفسير والمفسرون للذهبي — مختارات'],
                    ['slug' => 'tf-y4s2-aqeedah', 'name_ar' => 'العقيدة', 'primary_text_ar' => 'العقيدة الواسطية'],
                    ['slug' => 'tf-y4s2-ijaz', 'name_ar' => 'الإعجاز القرآني', 'primary_text_ar' => 'ضوابطه'],
                    ['slug' => 'tf-y4s2-muhkam', 'name_ar' => 'المحكم والمتشابه', 'primary_text_ar' => 'دراسة أصولية'],
                    ['slug' => 'tf-y4s2-nasikh', 'name_ar' => 'الناسخ والمنسوخ', 'primary_text_ar' => 'دراسة تطبيقية'],
                    ['slug' => 'tf-y4s2-makki', 'name_ar' => 'المكي والمدني', 'primary_text_ar' => 'دراسة تطبيقية'],
                    ['slug' => 'tf-y4s2-anwa', 'name_ar' => 'أنواع التفسير', 'primary_text_ar' => 'التفسير الموضوعي — التفسير التحليلي — التفسير الفقهي — التفسير العقدي'],
                ],
            ],
            'fifth-year' => [
                1 => [
                    ['slug' => 'tf-y5s1-tahlili', 'name_ar' => 'التفسير التحليلي المتقدم', 'primary_text_ar' => 'تطبيقات'],
                    ['slug' => 'tf-y5s1-mawdui', 'name_ar' => 'التفسير الموضوعي', 'primary_text_ar' => 'تطبيقات'],
                    ['slug' => 'tf-y5s1-kibar', 'name_ar' => 'مناهج كبار المفسرين', 'primary_text_ar' => 'الطبري وابن كثير والقرطبي والبغوي'],
                    ['slug' => 'tf-y5s1-tarjih', 'name_ar' => 'قواعد الترجيح', 'primary_text_ar' => 'بين أقوال المفسرين'],
                    ['slug' => 'tf-y5s1-mushkilat', 'name_ar' => 'مشكلات التفسير المعاصر', 'primary_text_ar' => 'دراسة وتحليل'],
                    ['slug' => 'tf-y5s1-shubuhat', 'name_ar' => 'شبهات حول القرآن', 'primary_text_ar' => 'الرد والتحليل'],
                    ['slug' => 'tf-y5s1-israiliyat', 'name_ar' => 'الإسرائيليات في التفسير', 'primary_text_ar' => 'دراسة نقدية'],
                    ['slug' => 'tf-y5s1-tatbiq', 'name_ar' => 'تطبيقات تفسيرية مكثفة', 'primary_text_ar' => 'تدريب عملي'],
                ],
                2 => [
                    ['slug' => 'tf-y5s2-surah', 'name_ar' => 'دراسة تطبيقية لسور مختارة', 'primary_text_ar' => 'تطبيق منهجي'],
                    ['slug' => 'tf-y5s2-takhrij', 'name_ar' => 'تخريج الأقوال التفسيرية', 'primary_text_ar' => 'تدريب'],
                    ['slug' => 'tf-y5s2-muqarana', 'name_ar' => 'الدراسة المقارنة بين التفاسير', 'primary_text_ar' => 'تطبيقات'],
                    ['slug' => 'tf-y5s2-modern', 'name_ar' => 'التفسير في العصر الحديث', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'tf-y5s2-orientalists', 'name_ar' => 'شبهات المستشرقين', 'primary_text_ar' => 'حول القرآن'],
                    ['slug' => 'tf-y5s2-research', 'name_ar' => 'حلقات بحث تفسيرية', 'primary_text_ar' => 'تطبيق'],
                    ['slug' => 'tf-y5s2-thesis', 'name_ar' => 'البحث التخصصي', 'primary_text_ar' => 'إعداد بحث تخصصي'],
                ],
            ],
        ],
        'fiqh-usul' => [
            'fourth-year' => [
                1 => [
                    ['slug' => 'fq-y4s1-usul', 'name_ar' => 'أصول الفقه المتقدم', 'primary_text_ar' => 'مذكرة أصول الفقه / معالم أصول الفقه للجيزاني / معاقد الفصول'],
                    ['slug' => 'fq-y4s1-qawaid', 'name_ar' => 'القواعد الفقهية', 'primary_text_ar' => 'دراسة وتطبيق'],
                    ['slug' => 'fq-y4s1-maqasid', 'name_ar' => 'مقاصد الشريعة', 'primary_text_ar' => 'الموافقات'],
                    ['slug' => 'fq-y4s1-ibadat', 'name_ar' => 'فقه العبادات', 'primary_text_ar' => 'دراسة تطبيقية'],
                    ['slug' => 'fq-y4s1-muamalat', 'name_ar' => 'فقه المعاملات', 'primary_text_ar' => 'دراسة تطبيقية'],
                    ['slug' => 'fq-y4s1-khilaf', 'name_ar' => 'الخلاف الفقهي', 'primary_text_ar' => 'آدابه وضوابطه'],
                    ['slug' => 'fq-y4s1-takhrij', 'name_ar' => 'تخريج الفروع على الأصول', 'primary_text_ar' => 'تدريب'],
                    ['slug' => 'fq-y4s1-madhahib', 'name_ar' => 'المذاهب الفقهية', 'primary_text_ar' => 'دراسة مقارنة'],
                    ['slug' => 'fq-y4s1-tatbiq', 'name_ar' => 'التطبيقات الفقهية', 'primary_text_ar' => 'مسائل تطبيقية'],
                ],
                2 => [
                    ['slug' => 'fq-y4s2-usul', 'name_ar' => 'أصول الفقه المتقدم', 'primary_text_ar' => 'مذكرة أصول الفقه / معالم أصول الفقه للجيزاني / معاقد الفصول'],
                    ['slug' => 'fq-y4s2-qawaid', 'name_ar' => 'القواعد الفقهية', 'primary_text_ar' => 'تطبيقاتها'],
                    ['slug' => 'fq-y4s2-usra', 'name_ar' => 'فقه الأسرة', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y4s2-jinayat', 'name_ar' => 'فقه الجنايات', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y4s2-qada', 'name_ar' => 'فقه القضاء', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y4s2-siyasa', 'name_ar' => 'السياسة الشرعية', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y4s2-maqasid', 'name_ar' => 'المقاصد الشرعية', 'primary_text_ar' => 'الموافقات'],
                    ['slug' => 'fq-y4s2-nawazil', 'name_ar' => 'فقه النوازل', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y4s2-mali', 'name_ar' => 'المعاملات المالية المعاصرة', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y4s2-khilaf', 'name_ar' => 'مسائل الخلاف الفقهي', 'primary_text_ar' => 'دراسة وترجيح'],
                ],
            ],
            'fifth-year' => [
                1 => [
                    ['slug' => 'fq-y5s1-takhrij', 'name_ar' => 'تخريج الفروع على الأصول', 'primary_text_ar' => 'تطبيقات متقدمة'],
                    ['slug' => 'fq-y5s1-qawaid-kubra', 'name_ar' => 'القواعد الفقهية الكبرى', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y5s1-muqarana', 'name_ar' => 'الفقه المقارن', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y5s1-nawazil', 'name_ar' => 'فقه النوازل', 'primary_text_ar' => 'دراسة متقدمة'],
                    ['slug' => 'fq-y5s1-mali', 'name_ar' => 'فقه المعاملات المالية المعاصرة', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y5s1-usra', 'name_ar' => 'فقه الأسرة', 'primary_text_ar' => 'القضايا المعاصرة'],
                    ['slug' => 'fq-y5s1-qada', 'name_ar' => 'فقه القضاء', 'primary_text_ar' => 'دراسة متقدمة'],
                    ['slug' => 'fq-y5s1-siyasa', 'name_ar' => 'فقه السياسة الشرعية', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y5s1-maqasid', 'name_ar' => 'المقاصد الشرعية', 'primary_text_ar' => 'تطبيقاتها — الموافقات'],
                    ['slug' => 'fq-y5s1-tatbiq', 'name_ar' => 'دراسات فقهية تطبيقية', 'primary_text_ar' => 'تدريب'],
                ],
                2 => [
                    ['slug' => 'fq-y5s2-tib', 'name_ar' => 'النوازل الطبية', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y5s2-iqtisad', 'name_ar' => 'النوازل الاقتصادية والمالية', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y5s2-usra', 'name_ar' => 'القضايا الأسرية المعاصرة', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y5s2-aqalliyat', 'name_ar' => 'فقه الأقليات', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'fq-y5s2-khilaf', 'name_ar' => 'دراسة الخلاف الفقهي', 'primary_text_ar' => 'الترجيح — تحرير محل النزاع — التدريب على تحرير المسائل'],
                    ['slug' => 'fq-y5s2-thesis', 'name_ar' => 'البحث الفقهي التخصصي', 'primary_text_ar' => 'إعداد بحث'],
                ],
            ],
        ],
        'hadith-sciences' => [
            'fourth-year' => [
                1 => [
                    ['slug' => 'hd-y4s1-mustalah', 'name_ar' => 'مصطلح الحديث المتقدم', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s1-rijal', 'name_ar' => 'علم الرجال', 'primary_text_ar' => 'الجرح والتعديل'],
                    ['slug' => 'hd-y4s1-takhrij', 'name_ar' => 'أصول التخريج', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s1-ilal', 'name_ar' => 'علل الحديث', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s1-hujiya', 'name_ar' => 'حجية السنة', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s1-difa', 'name_ar' => 'الدفاع عن السنة', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s1-bukhari', 'name_ar' => 'صحيح البخاري', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s1-muslim', 'name_ar' => 'صحيح مسلم', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s1-manahij', 'name_ar' => 'مناهج المحدثين', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s1-shuruh', 'name_ar' => 'شروح الحديث', 'primary_text_ar' => 'دراسة'],
                ],
                2 => [
                    ['slug' => 'hd-y4s2-ilal', 'name_ar' => 'علل الحديث', 'primary_text_ar' => 'تطبيقات'],
                    ['slug' => 'hd-y4s2-naqd', 'name_ar' => 'نقد الأسانيد والمتون', 'primary_text_ar' => 'تدريب'],
                    ['slug' => 'hd-y4s2-takhrij', 'name_ar' => 'التخريج المتقدم', 'primary_text_ar' => 'تدريب'],
                    ['slug' => 'hd-y4s2-mukhtalif', 'name_ar' => 'مختلف الحديث', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s2-mushkil', 'name_ar' => 'مشكل الحديث', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s2-sunan', 'name_ar' => 'كتب السنن', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s2-bukhari-manhaj', 'name_ar' => 'منهج الإمام البخاري', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s2-muslim-manhaj', 'name_ar' => 'منهج الإمام مسلم', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y4s2-shubuhat', 'name_ar' => 'شبهات منكري السنة', 'primary_text_ar' => 'الرد عليها'],
                ],
            ],
            'fifth-year' => [
                1 => [
                    ['slug' => 'hd-y5s1-ilal', 'name_ar' => 'علم علل الحديث', 'primary_text_ar' => 'دراسة متقدمة'],
                    ['slug' => 'hd-y5s1-takhrij', 'name_ar' => 'التخريج المتقدم', 'primary_text_ar' => 'تطبيقات'],
                    ['slug' => 'hd-y5s1-naqd', 'name_ar' => 'نقد الأسانيد والمتون', 'primary_text_ar' => 'تطبيقات'],
                    ['slug' => 'hd-y5s1-rijal', 'name_ar' => 'علم الرجال', 'primary_text_ar' => 'دراسة متقدمة'],
                    ['slug' => 'hd-y5s1-kutub-ilal', 'name_ar' => 'كتب العلل', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y5s1-sunan', 'name_ar' => 'كتب السنن', 'primary_text_ar' => 'دراسة متقدمة'],
                    ['slug' => 'hd-y5s1-shuruh', 'name_ar' => 'شروح الحديث', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y5s1-manahij', 'name_ar' => 'مناهج المحدثين', 'primary_text_ar' => 'دراسة متقدمة'],
                ],
                2 => [
                    ['slug' => 'hd-y5s2-takhrij', 'name_ar' => 'تطبيقات متقدمة في التخريج', 'primary_text_ar' => 'تدريب'],
                    ['slug' => 'hd-y5s2-hukm', 'name_ar' => 'الحكم على الأحاديث', 'primary_text_ar' => 'تطبيقات'],
                    ['slug' => 'hd-y5s2-asanid', 'name_ar' => 'دراسة الأسانيد والرجال', 'primary_text_ar' => 'تطبيقات'],
                    ['slug' => 'hd-y5s2-mukhtalif', 'name_ar' => 'مختلف الحديث ومشكله', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y5s2-rad', 'name_ar' => 'الرد على منكري السنة', 'primary_text_ar' => 'دراسة'],
                    ['slug' => 'hd-y5s2-shubuhat', 'name_ar' => 'شبهات الطاعنين في السنة', 'primary_text_ar' => 'الرد'],
                    ['slug' => 'hd-y5s2-muqarana', 'name_ar' => 'دراسة مقارنة لشروح الحديث', 'primary_text_ar' => 'تطبيق'],
                    ['slug' => 'hd-y5s2-research', 'name_ar' => 'حلقات بحث حديثية', 'primary_text_ar' => 'تطبيق'],
                    ['slug' => 'hd-y5s2-thesis', 'name_ar' => 'البحث الحديثي التخصصي', 'primary_text_ar' => 'إعداد ومناقشة'],
                ],
            ],
        ],
    ],

    'shared_specialized' => [
        ['slug' => 'research-methods', 'name_ar' => 'منهجية البحث العلمي'],
    ],
];
