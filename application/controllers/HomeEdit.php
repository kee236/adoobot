




    public function _time_zone_list()
    {
        return $timezones =
            array(
                'Asia/Bangkok' => '(GMT+7:00) Asia/Bangkok (Thailand Standard Time)',
                'Asia/Jakarta' => '(GMT+7:00) Asia/Jakarta (West Indonesia Time)',
                'Asia/Ho_Chi_Minh' => '(GMT+7:00) Asia/Ho_Chi_Minh (Vietnam Standard Time)',

                'Asia/Singapore' => '(GMT+8:00) Asia/Singapore (Singapore Standard Time)',
                'Asia/Kuala_Lumpur' => '(GMT+8:00) Asia/Kuala_Lumpur (Malaysia Standard Time)',
                'Asia/Manila' => '(GMT+8:00) Asia/Manila (Philippines Standard Time)',
                'Asia/Taipei' => '(GMT+8:00) Asia/Taipei (Taiwan Standard Time)',
                'Asia/Shanghai' => '(GMT+8:00) Asia/Shanghai (China Standard Time)',
                'Asia/Hong_Kong' => '(GMT+8:00) Asia/Hong_Kong (Hong Kong Standard Time)',

                'Asia/Seoul' => '(GMT+9:00) Asia/Seoul (Korea Standard Time)',
                'Asia/Tokyo' => '(GMT+9:00) Asia/Tokyo (Japan Standard Time)',

                'Asia/Kolkata' => '(GMT+5:30) Asia/Kolkata (India Standard Time)',

                // เพิ่มเติมสำหรับครอบคลุมกรณีอื่นๆ (อาจมีผู้ใช้นอกเหนือจากกลุ่มเป้าหมายหลัก)
                'Etc/GMT' => '(GMT+0:00) Etc/GMT (Greenwich Mean Time)',
                'Etc/UTC' => '(GMT+0:00) Etc/UTC (Universal Coordinated Time)',
                'America/Los_Angeles' => '(GMT-8:00) America/Los_Angeles (Pacific Standard Time)', // ตัวอย่างเขตเวลาอเมริกา
                'Europe/London' => '(GMT+0:00) Europe/London (British Summer Time)', // ตัวอย่างเขตเวลา Europe
            );
    }



    protected function get_country_names()
    {
        $countries = $this->get_country_iso_phone_currecncy('country');
        $country_dropdown = array("" => $this->lang->line("Select Country"));
        foreach ($countries as $iso => $name) {
            $flag_path = base_url("assets/img/flag/" . strtolower($iso) . ".png");
            $country_dropdown[$iso] = '<img src="' . $flag_path . '" style="width: 24px; height: 16px; margin-right: 5px; vertical-align: middle;">' . $name;
        }
        return $country_dropdown;
    }

    protected function get_language_names()
    {
        $array_languages = array(
            'ar-XA' => 'Arabic',
            'bg' => 'Bulgarian',
            'hr' => 'Croatian',
            'cs' => 'Czech',
            'da' => 'Danish',
            'de' => 'German',
            'el' => 'Greek',
            'en' => 'English',
            'et' => 'Estonian',
            'es' => 'Spanish',
            'fi' => 'Finnish',
            'fr' => 'French',
            'in' => 'Indonesian',
            'ga' => 'Irish',
            'hi' => 'Hindi', // แก้ไขจาก 'hr' เป็น 'hi'
            'hu' => 'Hungarian',
            'he' => 'Hebrew',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'lv' => 'Latvian',
            'lt' => 'Lithuanian',
            'nl' => 'Dutch',
            'no' => 'Norwegian',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'sv' => 'Swedish',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sr-CS' => 'Serbian',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'uk-UA' => 'Ukrainian',
            'zh-chs' => 'Chinese (Simplified)',
            'zh-cht' => 'Chinese (Traditional)'
        );

        $language_dropdown = array("" => $this->lang->line("Select Language"));
        foreach ($array_languages as $code => $name) {
            // สร้างชื่อไฟล์ไอคอนธงชาติจากโค้ดภาษา (อาจต้องปรับตามมาตรฐานชื่อไฟล์ของคุณ)
            $flag_code = strtolower(substr($code, 0, 2));
            $flag_path = base_url("assets/img/flag/" . $flag_code . ".png");
            $language_dropdown[$code] = '<img src="' . $flag_path . '" style="width: 24px; height: 16px; margin-right: 5px; vertical-align: middle;">' . $name;
        }
        return $language_dropdown;
    }
