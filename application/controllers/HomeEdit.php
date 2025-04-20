




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
