<div align="center">
  <img src="assets/images/satit-PSU.png" alt="satit-helpdesk Logo" width="240px">
  <h1>satit-helpdesk - ระบบแจ้งซ่อมโรงเรียนสาธิต</h1>
  <p>
    ระบบแจ้งซ่อมคอมพิวเตอร์และอุปกรณ์ IT แบบ Web-based สำหรับโรงเรียนสาธิต<br />
    พัฒนาขึ้นเพื่อเพิ่มประสิทธิภาพและจัดระเบียบการทำงาน เน้นความเรียบง่ายและฟังก์ชันที่ครบครัน
  </p>
  
  <!-- Badges/Shields -->
  <p>
    <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Badge"/>
    <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL Badge"/>
    <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript Badge"/>
    <img src="https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap Badge"/>
    <img src="https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white" alt="jQuery Badge"/>
  </p>
</div>

---

## 🌟 ภาพรวมและฟังก์ชันการทำงานหลัก (Overview & Features)

**satit-helpdesk** เป็นโปรเจกต์ที่พัฒนาขึ้นเพื่อแก้ปัญหาการจัดการงานซ่อมในโรงเรียนสาธิต โดยมีเป้าหมายเพื่อสร้างระบบที่ทันสมัย, ใช้งานง่าย, และช่วยลดขั้นตอนการทำงานของเจ้าหน้าที่ IT ได้อย่างมีประสิทธิภาพ

### 👤 **ฝั่งผู้ใช้งานทั่วไป (เจ้าหน้าที่)**
* **📱 หน้าแจ้งซ่อมที่ทันสมัย:** UI/UX ที่ออกแบบมาให้ใช้งานง่าย สวยงาม และรองรับการแสดงผลบนมือถือ (Responsive)
* **📝 ฟอร์มแจ้งซ่อม:** กรอกรายละเอียดปัญหา, เลือกวันที่, สถานที่, และผู้แจ้ง
* **🔍 Dropdown แบบค้นหาได้:** ช่วยให้ค้นหาสถานที่และรายชื่อผู้แจ้งได้อย่างรวดเร็วด้วย Select2
* **✅ ยืนยันการแจ้งซ่อม:** ใช้ SweetAlert2 เพื่อยืนยันการส่งข้อมูลอย่างสวยงาม

### ⚙️ **ฝั่งผู้ดูแลระบบ (Admin)**
* **🔑 ระบบ Login:** หน้าสำหรับยืนยันตัวตนเพื่อเข้าสู่ส่วนจัดการระบบ
* **📊 Advance Dashboard:**
    * การ์ดสรุปจำนวนงานในสถานะต่างๆ (รอรับเรื่อง, กำลังดำเนินการ, เสร็จสิ้น)
    * **กราฟแสดงสถิติ (Chart.js):**
        * กราฟแท่งสรุปจำนวนงานรายเดือน
        * กราฟวงกลมสรุปสัดส่วนงานตามประเภท
        * กราฟแท่งแบบซ้อนแสดงสถิติงามตามสถานที่รายเดือน
* **📋 ระบบจัดการงาน (Workflow):**
    * รับเรื่องการแจ้งซ่อมใหม่จากหน้า Dashboard หรือหน้ารายการ
    * บันทึกรายละเอียดการซ่อม (สาเหตุ, วิธีแก้ไข)
    * เปลี่ยนสถานะของงาน (เสร็จสิ้น, ซ่อมไม่ได้, ส่งซ่อมต่อ)
* **🗃️ ระบบจัดการข้อมูลหลัก (Master Data):**
    * จัดการ (เพิ่ม/ลบ/แก้ไข) ข้อมูลสถานที่, ผู้แจ้ง, และประเภทงานซ่อม
    * ตารางข้อมูลขั้นสูง (DataTables) พร้อมระบบค้นหา, แบ่งหน้า, และเรียงข้อมูล
* **⏱️ ระบบติดตามเวลาการแก้ไข:**
    * คำนวณและบันทึกเวลาที่ใช้ในการแก้ไขโดยอัตโนมัติ
    * แสดงเวลาการดำเนินงานในรายการและรายงาน
* **📄 ระบบรายงาน (Reporting):**
    * กรองข้อมูลได้หลายมิติ: ช่วงวันที่, สถานที่, ประเภท, ผู้ดำเนินการ, สถานะ
    * **Export ข้อมูล:** สามารถส่งออกเป็นไฟล์ Excel, CSV หรือสั่งพิมพ์ได้ (รองรับภาษาไทย)
* **📱 การแจ้งเตือนผ่าน Telegram:** ส่งข้อความแจ้งเตือนเข้ากลุ่มทันทีเมื่อมีรายการแจ้งซ่อมใหม่
* **➕ ฟังก์ชันเสริม:**
    * **PWA Ready:** สามารถสร้างทางลัดพร้อมไอคอนบน Desktop เพื่อเข้าใช้งานได้เหมือนแอปพลิเคชัน

---

## 🛠️ เทคโนโลยีที่ใช้ (Tech Stack)

* **Backend:** PHP 7.x/8.x
* **Frontend:** HTML5, CSS3, JavaScript (ES6)
* **Database:** MySQL / MariaDB
* **Libraries & Frameworks:**
    * [Bootstrap 5](https://getbootstrap.com/): สำหรับโครงสร้างและ UI พื้นฐาน
    * [jQuery](https://jquery.com/): Dependency สำหรับไลบรารีบางตัว
    * [SweetAlert2](https://sweetalert2.github.io/): สำหรับ Pop-up แจ้งเตือน
    * [DataTables](https://datatables.net/): สำหรับตารางข้อมูลขั้นสูง (ค้นหา, แบ่งหน้า, Export)
    * [Chart.js](https://www.chartjs.org/): สำหรับสร้างกราฟแสดงผลข้อมูล
    * [Select2](https://select2.org/): สำหรับ Dropdown แบบค้นหาได้
    * [Axios](https://axios-http.com/): สำหรับการทำ AJAX Request

---

## 🚀 การติดตั้งและใช้งาน (Installation)

1.  **Clone Repository:**
    ```bash
    git clone [https://github.com/your-username/satit-helpdesk.git](https://github.com/your-username/satit-helpdesk.git)
    cd satit-helpdesk
    ```

2.  **สร้างฐานข้อมูล:**
    * สร้างฐานข้อมูลใหม่ใน phpMyAdmin ชื่อว่า `satit_helpdesk` (หรือชื่ออื่นตามต้องการ)
    * Import ไฟล์ `.sql` ที่มีโครงสร้างตารางและข้อมูลพื้นฐานเข้าไปในฐานข้อมูลที่สร้างขึ้น
        >*(คำแนะนำ: คุณควร Export ฐานข้อมูลปัจจุบันของคุณออกมาเป็นไฟล์ `.sql` เพื่อนำมาใช้ในขั้นตอนนี้)*

3.  **ตั้งค่าการเชื่อมต่อฐานข้อมูล:**
    * แก้ไขไฟล์ `db_connect.php`
    * เปลี่ยนค่า `$servername`, `$username`, `$password`, และ `$dbname` ให้ตรงกับการตั้งค่าเซิร์ฟเวอร์ของคุณ
    ```php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "satit_helpdesk";
    ```

4.  **ตั้งค่า Telegram (ถ้าต้องการใช้งาน):**
    * แก้ไขไฟล์ `config.php`
    * ใส่ `TELEGRAM_BOT_TOKEN` และ `TELEGRAM_CHAT_ID` ของคุณ
    ```php
    define('TELEGRAM_BOT_TOKEN', 'YOUR_BOT_TOKEN_HERE');
    define('TELEGRAM_CHAT_ID', 'YOUR_CHAT_ID_HERE');
    ```

5.  **ข้อมูลสำหรับ Login:**
    * Username: `admin`
    * Password: `password123`
    >*(สามารถเพิ่ม/แก้ไขข้อมูล Admin ได้จากตาราง `admins` ในฐานข้อมูล)*

6.  **เข้าใช้งาน:**
    * หน้าแจ้งซ่อม: `http://localhost/satit-helpdesk/`
    * หน้า Admin: `http://localhost/satit-helpdesk/admin/`

---

## 📝 ผู้จัดทำ

* **[แวอาลี แวสะแม]** - *ผู้พัฒนาระบบ satit-helpdesk* - [ลิงก์ไปยัง GitHub ของคุณ]

พัฒนาร่วมกับ Gemini (Google AI)
