<?


<div class="alert alert-secondary alert-dismissible show fade">
    <div class="alert-body">
        <button class="close" data-dismiss="alert">
            <span>×</span>
        </button>
        <?php
            // แสดงข้อความแจ้งเตือนเกี่ยวกับปุ่ม 'Get Started', 'No Match' และปุ่ม Action อื่นๆ
            // ข้อความนี้จะถูกดึงมาจากไฟล์ภาษา (Language File)
            echo $this->lang->line('Action button like Get Started, No Match etc are available in Action Button Settings Tab.');
        ?>
    </div>
</div>

<div class="table-responsive data-card">
  <?php
    // ตารางสำหรับแสดงข้อมูล Visual Flow Builder
    // มีการใช้ Bootstrap class เพื่อให้ตาราง responsive และสวยงาม
  ?>
  <table class="table table-bordered table-sm table-striped" id="mytable">
    <thead>
      <tr>
        <th>#</th>
        <?php
            // ส่วนหัวตารางดึงข้อความมาจากไฟล์ภาษา (Language File) เพื่อรองรับหลายภาษา
        ?>
        <th><?php echo $this->lang->line("Template ID"); ?></th>
        <th><?php echo $this->lang->line("Reference Name"); ?></th>
        <th><?php echo $this->lang->line("Page Name"); ?></th>
        <th><?php echo $this->lang->line("Media Type"); ?></th>
        <th style="min-width: 300px"><?php echo $this->lang->line("Actions"); ?></th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>

<?php
    // Modal สำหรับเลือกเพจ (Page Selection Modal)
    // ใช้ Bootstrap Modal และตั้งค่า data-backdrop="static" data-keyboard="false"
    // เพื่อป้องกันการปิด Modal โดยไม่ตั้งใจ
?>
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="BotcopyModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <?php
            // ส่วนหัว Modal แสดงหัวข้อ 'Page Selections' ดึงมาจากไฟล์ภาษา
        ?>
        <h3 class="modal-title"><?php echo $this->lang->line('Page Selections'); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php
            // Hidden fields สำหรับเก็บค่า ID และ Media Type สำหรับการอัปเดต
        ?>
        <input type="hidden" id="pageselcetion-update-id" value="0">
        <input type="hidden" id="pageselcetion-update-mediatype" value="fb">

        <div class="form-group">
            <?php
                // Dropdown สำหรับเลือกเพจ
                // ใช้ form_dropdown ของ CodeIgniter และ Bootstrap class 'form-control select2'
            ?>
            <label><?php echo $this->lang->line("Please select a page"); ?></label>
            <?php
            $page_list[''] = $this->lang->line("Choose a Page"); // กำหนดค่าเริ่มต้นสำหรับ dropdown
            echo form_dropdown('page_list_option',$page_list,'','id="page_list_option" class="form-control select2" style="width:100%;"');
            ?>
        </div>
      </div>
      <div class="modal-footer">
        <?php
            // ปุ่มปิด Modal ดึงข้อความ 'Close' จากไฟล์ภาษา
        ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
        <?php
            // ปุ่มบันทึกการเปลี่ยนแปลง ดึงข้อความ 'Save changes' จากไฟล์ภาษา
        ?>
        <button type="button" class="btn btn-primary" id="page-change-save"><?php echo $this->lang->line('Save changes'); ?></button>
      </div>
    </div>
  </div>
</div>

<?php
    // เตรียมตัวแปรสำหรับข้อความยืนยันและ URL สำหรับสร้าง Flow ใหม่
    // ข้อความ "are you sure" ดึงมาจากไฟล์ภาษา
    $areyousure = $this->lang->line("are you sure");
    // สร้าง URL สำหรับโหลด Visual Flow Builder
    $builder_load_url = base_url("visual_flow_builder/load_builder/{$page_auto_id}/1/{$media_type}");
    // สร้าง HTML สำหรับปุ่ม "Create New Flow" ที่จะนำไปแสดงใน DataTable
    $drop_menu = '<a href="'.$builder_load_url.'" class="float-right btn btn-primary" type="button" target="_BLANK"><i class="fas fa-plus-circle"></i> '.$this->lang->line("Create New Flow").'</a>';
?>

<script>
$(document).ready(function(){

    // นำปุ่ม "Create New Flow" ไปเพิ่มในส่วน filter ของ DataTable
    var drop_menu = '<?php echo $drop_menu;?>';
    setTimeout(function(){
      $("#mytable_filter").append(drop_menu);
    }, 1000); // ดีเลย์ 1 วินาทีเพื่อให้ DataTable โหลดเสร็จก่อน

    var base_url="<?php echo base_url(); ?>";
    var page_auto_id = "<?php echo $page_auto_id; ?>";
    var data_url;

    // กำหนด URL สำหรับการดึงข้อมูล DataTable
    if(page_auto_id != 0) {
      data_url = base_url+'visual_flow_builder/visual_flow_builder_data/'+page_auto_id+'?media_type='+selected_global_media_type;
    } else {
      data_url = base_url+'visual_flow_builder/visual_flow_builder_data'+'?media_type='+selected_global_media_type;
    }

    // ส่วนของ DataTable
    var table = $("#mytable").DataTable({
        serverSide: true, // ประมวลผลข้อมูลบน Server Side
        processing:true,  // แสดงสถานะ "Processing..."
        bFilter: true,    // เปิดใช้งาน Filter (ช่องค้นหา)
        order: [[ 1, "desc" ]], // จัดเรียงข้อมูลเริ่มต้นที่คอลัมน์ที่ 1 (Template ID) แบบ Descending
        pageLength: 10,   // แสดง 10 แถวต่อหน้า
        ajax:
        {
            "url": data_url, // URL สำหรับดึงข้อมูลผ่าน AJAX
            "type": 'POST',  // ใช้เมธอด POST
            "dataSrc": function ( json )
            {
              // ฟังก์ชันเรียกใช้หลังจากดึงข้อมูลสำเร็จ
              $(".table-responsive").niceScroll(); // ทำให้ scrollbar ของตารางสวยงามขึ้น (ต้องมีการติดตั้ง niceScroll)
              return json.data; // คืนค่าข้อมูลสำหรับ DataTable
            }
        },
        language:
        {
          // กำหนดไฟล์ภาษาสำหรับ DataTable โดยอ้างอิงจากภาษาที่เลือกในระบบ
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">', // กำหนดโครงสร้างของ DOM ที่ DataTable สร้างขึ้น
        columnDefs: [ // กำหนดคุณสมบัติของแต่ละคอลัมน์
            {
              targets: [1,3,4], // คอลัมน์ที่ 1 (Template ID), 3 (Page Name), 4 (Media Type)
              visible: false   // ซ่อนคอลัมน์เหล่านี้
            },
            {
              targets: [0,5], // คอลัมน์ที่ 0 (#), 5 (Actions)
              className: 'text-center', // จัดให้อยู่กึ่งกลาง
              sortable: false           // ไม่สามารถจัดเรียงได้
            },
            {
                targets: [4], // คอลัมน์ Media Type
                "render": function ( data, type, row, meta )
                {
                    // แปลงค่า media_type (ig/fb) ให้เป็นข้อความที่อ่านง่าย (Instagram/Facebook)
                    var media_type = row[4];
                    var str = '';
                    if(media_type == 'ig')
                        str = 'Instagram';
                    else
                        str = 'Facebook';
                    return str;
                }
            },
            {
                targets: [5], // คอลัมน์ Actions (การกระทำ)
                "render": function ( data, type, row, meta )
                {
                    var id = row[1]; // ID ของแถว (Template ID)
                    var media_type = row[4]; // ประเภท Media (ig/fb)
                    <?php
                        // ดึงข้อความสำหรับปุ่ม Actions จากไฟล์ภาษา
                        // ข้อความเหล่านี้จะถูกใช้ใน JavaScript เพื่อสร้างปุ่ม
                    ?>
                    var copy_str="<?php echo $this->lang->line('Copy to other Page');?>";
                    var duplicate_str="<?php echo $this->lang->line('Duplicate');?>";
                    var export_str="<?php echo $this->lang->line('Export flow data');?>";
                    var edit_str="<?php echo $this->lang->line('Edit');?>";
                    var delete_str="<?php echo $this->lang->line('Delete');?>";

                    // โค้ดส่วนที่เหลือของ render function จะเป็น Logic ในการสร้างปุ่ม Actions
                    // ซึ่งจะยังไม่ครบถ้วนจากโค้ดที่ให้มา


                    // สร้าง URL สำหรับปุ่ม 'Duplicate' (ทำซ้ำ)
                    var duplicate_url = base_url + "visual_flow_builder/duplicate_builder_data/" + id + "/1/" + media_type;
                    // สร้าง URL สำหรับปุ่ม 'Export' (ส่งออก)
                    var export_url = base_url + "visual_flow_builder/export_builder_data/" + id + "/1/" + media_type;
                    // สร้าง URL สำหรับปุ่ม 'Edit' (แก้ไข)
                    var edit_url = base_url + "visual_flow_builder/edit_builder_data/" + id + "/1/" + media_type;

                    // เริ่มต้นสร้าง HTML string สำหรับปุ่ม Actions แต่ละปุ่ม
                    // ปุ่ม Duplicate (ทำซ้ำ)
                    str="&nbsp;<a target='_blank' class='text-center btn btn-circle btn-outline-primary' href='"+duplicate_url+"' title='"+duplicate_str+"'>"+'<i class="fa fa-clone"></i>'+"</a>";
                    // ปุ่ม Copy to other Page (คัดลอกไปยังเพจอื่น)
                    // มีการเพิ่ม class 'botcopy' และเก็บค่า id กับ media_type ใน attribute
                    str=str+"&nbsp;<a target='_blank' class='text-center btn btn-circle btn-outline-info botcopy' href='#' title='"+copy_str+"' value='"+id+"' media_type='"+media_type+"'>"+'<i class="fa fa-copy"></i>'+"</a>";
                    // ปุ่ม Export flow data (ส่งออกข้อมูล Flow)
                    str=str+"&nbsp;<a target='_blank' class='text-center btn btn-circle btn-outline-success' href='"+export_url+"' title='"+export_str+"'>"+'<i class="fas fa-file-export"></i>'+"</a>";
                    // ปุ่ม Edit (แก้ไข)
                    // สังเกตว่ามี edit_url และ edit_reply_info handler ด้านล่าง ควรเลือกใช้อันใดอันหนึ่ง
                    str=str+"&nbsp;<a target='_blank' class='text-center btn btn-circle btn-outline-warning' href='"+edit_url+"' title='"+edit_str+"'>"+'<i class="fa fa-edit"></i>'+"</a>";

                    // ปุ่ม Delete (ลบ)
                    // มี class 'delete_data' และเก็บ table_id เพื่อใช้ในการลบ
                    str=str+"&nbsp;<a name='delete' href='#' class='text-center delete_data btn btn-circle btn-outline-danger ' title='"+delete_str+"' table_id="+id+" '>"+'<i class="fa fa-trash"></i>'+"</a>";

                    return str; // คืนค่า HTML string ของปุ่ม Actions
                }
            }
        ]
    });
    // สิ้นสุดส่วน DataTable

    // เมื่อคลิกปุ่ม 'add' (ซึ่งไม่ได้อยู่ในโค้ดที่ให้มา แต่อาจเป็นปุ่มสำหรับเพิ่มข้อมูล)
    $('#add').click(function(e){
        e.preventDefault(); // ป้องกันการกระทำเริ่มต้นของลิงก์/ปุ่ม
        $('#dynamic_field_modal').modal('show'); // แสดง Modal ชื่อ 'dynamic_field_modal'
    });

    // เมื่อคลิกที่ปุ่ม 'Copy to other Page' (มี class 'botcopy')
    $(document).on('click','.botcopy',function(e){
        e.preventDefault(); // ป้องกันการกระทำเริ่มต้น
        var id = $(this).attr('value'); // ดึงค่า ID ของ Flow จาก attribute 'value'
        var media_type = $(this).attr('media_type'); // ดึงประเภทสื่อจาก attribute 'media_type'
        var modal_id = '#BotcopyModal'; // กำหนด ID ของ Modal ที่จะแสดง
        $('#pageselcetion-update-id').val(id); // กำหนดค่า ID ใน hidden input ของ Modal
        $('#pageselcetion-update-mediatype').val(media_type); // กำหนดค่า Media Type ใน hidden input ของ Modal
        $(modal_id).modal('show'); // แสดง Modal 'BotcopyModal'
    });

    // เมื่อคลิกปุ่ม 'submit' (ซึ่งไม่ได้อยู่ในโค้ดที่ให้มา แต่อาจเป็นปุ่มในฟอร์มอื่น)
    $('#submit').click(function(e) {
       e.preventDefault(); // ป้องกันการกระทำเริ่มต้น
       var page_id_media = $('#page_table_id').val(); // ดึงค่า page_id_media จาก element
       var page_id_media_array = page_id_media.split("-"); // แยก string ด้วยเครื่องหมาย "-"

       var page_table_id = page_id_media_array[0]; // ส่วนแรกคือ page_table_id
       var media_type = 'fb'; // กำหนดค่า media_type เริ่มต้นเป็น 'fb'
       if (typeof page_id_media_array[1] !== 'undefined') {
         media_type = page_id_media_array[1]; // ถ้ามีส่วนที่สอง ให้ใช้เป็น media_type
       }

       // ตรวจสอบว่า page_table_id ถูกเลือกหรือไม่
       if(page_table_id == '')
       {
          // แสดงข้อความเตือนถ้าไม่ได้เลือกเพจ
          swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("You have to select a page"); ?>', 'warning');
          return false; // หยุดการทำงาน
       }
       else
       {
          // สร้างลิงก์สำหรับโหลด Visual Flow Builder ด้วย page_table_id และ media_type
          var link = base_url + "visual_flow_builder/load_builder/" + page_table_id + "/1/" + media_type;
          window.location.replace(link); // เปลี่ยนหน้าไปยังลิงก์ที่สร้างขึ้น
       }

    });

    // เมื่อคลิกที่ปุ่ม 'Edit Reply Info' (มี class 'edit_reply_info')
    // สังเกตว่านี่เป็นอีกวิธีในการแก้ไข ซึ่งอาจซ้ำซ้อนกับปุ่ม 'Edit' ใน DataTable
    $(document).on('click', '.edit_reply_info', function(event) {
      event.preventDefault(); // ป้องกันการกระทำเริ่มต้น
      var table_id = $(this).attr('table_id'); // ดึงค่า ID
      var media_type = $(this).attr('media_type'); // ดึงประเภทสื่อ
      var link = base_url + "visual_flow_builder/edit_builder_data/" + table_id + "/1/" + media_type; // สร้างลิงก์แก้ไข
      window.location.replace(link); // เปลี่ยนหน้าไปยังลิงก์ที่สร้างขึ้น
    });

    // เมื่อคลิกปุ่ม 'Save changes' ใน Modal (ID 'page-change-save')
    $(document).on('click','#page-change-save',function(e){
        e.preventDefault(); // ป้องกันการกระทำเริ่มต้น
        var id =$('#pageselcetion-update-id').val(); // ดึง ID จาก hidden input
        var media_type =$('#pageselcetion-update-mediatype').val(); // ดึง Media Type จาก hidden input
        var selectElement = document.getElementById('page_list_option'); // อ้างอิงถึง dropdown เลือกเพจ
        var page_id = selectElement.value; // ดึงค่า page_id ที่เลือก
        var modal_id = '#BotcopyModal'; // กำหนด ID ของ Modal

        // ส่งข้อมูลผ่าน AJAX เพื่อคัดลอก Flow
        $.ajax({
            url: base_url+'visual_flow_builder/copy_builder_data', // URL ของ Controller ที่จัดการการคัดลอก
            type: "POST", // ใช้เมธอด POST
            data: {id,page_id,media_type}, // ข้อมูลที่ส่งไป
            dataType: 'json', // คาดหวังข้อมูลกลับมาเป็น JSON
            success:function(response) // เมื่อ AJAX สำเร็จ
            {
                $(modal_id).modal('hide'); // ซ่อน Modal

                if(response.status == "1") { // ถ้าสถานะเป็น 1 (สำเร็จ)
                    var id = response.id; // ดึง ID ใหม่จาก response
                    var media_type = response.media_type; // ดึง Media Type ใหม่จาก response

                    var final_url = base_url + 'visual_flow_builder/edit_builder_data/' + id + '/1/' + media_type;
                    window.open(final_url, '_blank'); // เปิดหน้าแก้ไข Flow ที่คัดลอกในแท็บใหม่
                }
                else{ // ถ้าสถานะไม่เป็น 1 (เกิดข้อผิดพลาด)
                    swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error'); // แสดงข้อความผิดพลาด
                }
            },
            // ในกรณีที่เกิดข้อผิดพลาดในการเรียก AJAX (เช่น Server Error)
            error:function(response){
                var span = document.createElement("span");
                span.innerHTML = response.responseText; // แสดง responseText ใน Alert
                swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
            }
        });

    });

    // เมื่อคลิกปุ่ม 'Delete' (มี class 'delete_data')
    $(document).on('click', '.delete_data', function(event) {
        event.preventDefault(); // ป้องกันการกระทำเริ่มต้น
        // แสดง Alert ยืนยันการลบ
        swal({
            title: '<?php echo $this->lang->line("Warning"); ?>', // หัวข้อเตือน
            text: '<?php echo $this->lang->line("Are you sure you want to delete this campaign"); ?>', // ข้อความยืนยัน
            icon: 'warning', // ไอคอนเตือน
            buttons: true, // แสดงปุ่มตกลง/ยกเลิก
            dangerMode: true, // เน้นปุ่มลบเป็นสีแดง
        })
        .then((willreset) => { // เมื่อผู้ใช้เลือกปุ่ม
            if (willreset) // ถ้าผู้ใช้กด 'OK' หรือ 'ตกลง'
            {
                $(this).addClass('btn-progress'); // แสดงสถานะโหลดบนปุ่ม
                var table_id = $(this).attr('table_id'); // ดึง ID ที่ต้องการลบ

                // ส่ง AJAX request ไปลบข้อมูล
                $.ajax({
                    context: this, // กำหนด context ของ AJAX ให้เป็นปุ่มที่คลิก
                    type:'POST', // ใช้เมธอด POST
                    url: base_url + "visual_flow_builder/delete_flowbuilder_data", // URL สำหรับลบข้อมูล
                    dataType: 'json', // คาดหวังข้อมูลกลับมาเป็น JSON
                    data: {table_id}, // ข้อมูลที่ส่งไป (ID ที่จะลบ)
                    success:function(response){ // เมื่อ AJAX สำเร็จ
                        $(this).removeClass('btn-progress'); // ลบสถานะโหลด

                        if(response.status == 1) // ถ้าสถานะเป็น 1 (ลบสำเร็จ)
                        {
                            // แสดงข้อความสำเร็จและรีโหลด DataTable
                            swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                                table.draw(); // รีโหลด DataTable เพื่ออัปเดตข้อมูล
                            });
                        }
                        else // ถ้าสถานะไม่เป็น 1 (เกิดข้อผิดพลาด)
                        {
                            swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error'); // แสดงข้อความผิดพลาด
                        }
                    },
                    error:function(response){ // เมื่อเกิดข้อผิดพลาดในการเรียก AJAX (เช่น Server Error)
                        var span = document.createElement("span");
                        span.innerHTML = response.responseText; // แสดง responseText ใน Alert
                        swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
                    }
                });
            }
        });
    });

});
</script>
