<?php 
include("db.php");
include("security.php");
requirelogin();

$departmentCount = 2;

$studentQuery = $conn->query("SELECT COUNT(*) as total FROM students");
$studentCount = $studentQuery ? $studentQuery->fetch_assoc()['total'] : 0;

$staffQuery = $conn->query("SELECT COUNT(*) as total FROM staff WHERE deleted=0");
$staffCount = $staffQuery ? $staffQuery->fetch_assoc()['total'] : 0;

$user_id = $_SESSION['user_id'];
$now = date("Y-m-d H:i:s");

$unread = $conn->query("
    SELECT COUNT(*) as total
    FROM notices n
    LEFT JOIN notice_reads r 
    ON n.id = r.notice_id AND r.user_id = $user_id
    WHERE r.id IS NULL
    AND n.expire_at > '$now'
")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>FIT Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
margin:0;
font-family:Arial;
background:#f4f6fb;
display:flex;
}

/* SIDEBAR */
.sidebar{
width:240px;
background:#0a2a66;
color:white;
height:100vh;
position:fixed;
padding-top:30px;
transition:0.3s;
z-index:1000;
}

.sidebar h3{
text-align:center;
margin-bottom:20px;
}

.sidebar a{
display:block;
color:white;
text-decoration:none;
padding:12px 20px;
margin:6px 15px;
border-radius:8px;
background:rgba(255,255,255,0.05);
}

.sidebar a:hover{
background:white;
color:#0a2a66;
}

/* MAIN */
.main{
margin-left:240px;
width:100%;
position:relative;
z-index:1;
}

/* HEADER */
.header{
background:white;
padding:10px 18px;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

.header h2{
color:#0a2a66;
margin:0;
font-size:20px;
}

.menu-toggle{
display:none;
font-size:22px;
cursor:pointer;
margin-right:10px;
}

.nav-btn{
background:red;
color:white;
padding:7px 12px;
border-radius:6px;
text-decoration:none;
font-weight:bold;
}

/* CONTENT */
.content{
padding:18px 22px;
}

/* WELCOME */
.main-card{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
text-align:center;
margin-bottom:15px;
}

.main-card h2{
margin:0;
color:#0a2a66;
}

.main-card p{
margin-top:8px;
color:#555;
line-height:1.6;
}

/* CARDS */
.cards{
display:flex;
justify-content:center;
gap:20px;
flex-wrap:nowrap;
margin-top:10px;
}

.card{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 5px 12px rgba(0,0,0,0.08);
display:flex;
justify-content:space-between;
align-items:center;
min-width:220px;
}

.card h3{margin:0;color:#0a2a66;}
.card p{margin:5px 0 0;font-size:13px;color:#666;}

.num{
font-size:26px;
font-weight:bold;
color:#0a2a66;
}

.icon{
font-size:30px;
opacity:0.7;
}

/* PRINCIPAL MESSAGE (UNCHANGED) */
.principal-section{
margin-top:20px;
width:100%;
min-height:420px;
position:relative;
background:url('img/fit.jpeg') center/cover no-repeat;
display:flex;
align-items:center;
padding:40px;
box-sizing:border-box;
}

.principal-section::before{
content:"";
position:absolute;
top:0;
left:0;
width:100%;
height:100%;
background:linear-gradient(
    90deg,
    rgba(255,255,255,0.85) 0%,
    rgba(255,255,255,0.60) 55%,
    rgba(255,255,255,0.25) 100%
);
}

.principal-content{
position:relative;
width:100%;
color:#111;
line-height:1.8;
font-size:15px;
text-align:justify;
}

.mobile-msg{display:none;}

/* POPUP */
.popup{
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.6);
justify-content:center;
align-items:center;
z-index:9999;
}

.popup-content{
background:white;
padding:25px;
border-radius:12px;
width:90%;
max-width:400px;
text-align:center;
position:relative;
}

.popup-content a{
display:block;
background:#0a2a66;
color:white;
padding:12px;
margin-top:15px;
border-radius:8px;
text-decoration:none;
}

.close-btn{
position:absolute;
top:10px;
right:15px;
font-size:22px;
cursor:pointer;
}

/* OVERLAY FIX */
.overlay{
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.4);
z-index:900;
}

/* SIDEBAR ABOVE OVERLAY */
.sidebar{
z-index:1000;
position:fixed;
}

/* =========================
   LOCATION BUTTON
========================= */
.location-btn{
padding:14px 22px;
background:#0a2a66;
color:white;
border:none;
border-radius:10px;
font-size:16px;
font-weight:bold;
cursor:pointer;
transition:0.3s;
box-shadow:0 4px 12px rgba(0,0,0,0.15);
}

.location-btn:hover{
background:#081f4d;
transform:translateY(-2px);
}

/* =========================
   MAP POPUP
========================= */
.map-popup{
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.5);
z-index:3000;
justify-content:center;
align-items:center;
padding:15px;
}

.map-box{
background:white;
width:100%;
max-width:700px;
border-radius:18px;
padding:20px;
position:relative;
animation:popup 0.3s ease;
}

.map-box h2{
margin-top:0;
color:#0a2a66;
text-align:center;
margin-bottom:15px;
}

.map-box iframe{
width:100%;
height:400px;
border:none;
border-radius:12px;
}

.close-map{
position:absolute;
top:10px;
right:18px;
font-size:30px;
cursor:pointer;
color:#0a2a66;
font-weight:bold;
}

/* =========================
   LOCATION BUTTON (SIDEBAR STYLE MATCH)
========================= */
.sidebar a.location-link{
background:rgba(255,255,255,0.08);
font-weight:bold;
}

.sidebar a.location-link:hover{
background:white;
color:#0a2a66;
}

.open-map-btn{
display:block;
margin-top:15px;
text-align:center;
background:#25d366;
color:white;
padding:12px;
border-radius:10px;
text-decoration:none;
font-weight:bold;
transition:0.3s;
}

.open-map-btn:hover{
background:#1da851;
}

/* POPUP ANIMATION */
@keyframes popup{

from{
transform:scale(0.9);
opacity:0;
}

to{
transform:scale(1);
opacity:1;
}

}

/* MOBILE */
@media(max-width:768px){

.map-box{
padding:15px;
border-radius:15px;
}

.map-box iframe{
height:300px;
}

.location-btn{
width:100%;
font-size:15px;
}

}

/* MOBILE FIX ONLY */
@media (max-width:768px){

body{flex-direction:column;}

.sidebar{
left:-240px;
transition:0.3s;
}

.sidebar.active{
left:0;
}

.main{
margin-left:0;
}

.menu-toggle{
display:block;
}

.cards{
flex-wrap:wrap;
}

.desktop-msg{display:none;}
.mobile-msg{display:block;}

.principal-section{
padding:20px;
}

}

/* NOTICE BAR */
.notice-bar{
background:#0a2a66;
color:white;
padding:8px 15px;
font-size:14px;
display:flex;
align-items:center;
gap:10px;
}

.notice-bar marquee{
flex:1;
font-weight:500;
}

</style>
</head>

<body>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

<h3>MENU</h3>

<a href="index.php">Home</a>
<a href="#" onclick="openDepartments()">Departments</a>
<a href="staff.php">Staff</a>

<a href="notices.php" style="position:relative;">
Notices
<span id="noticeBadge" style="background:red;color:white;border-radius:50%;padding:2px 7px;font-size:12px;position:absolute;top:0;right:-10px;display:none;">0</span>
</a>
<a href="javascript:void(0)" onclick="openLocation()"> Location</a>
<a href="about.php">About FIT</a>
<a href="contact.php">Contact Us</a>
</div>

<!-- MAIN -->
<div class="main">

<div class="notice-bar">
📢 Latest Notice:
<marquee id="noticeText">Loading...</marquee>
</div>

<div class="header">
<span class="menu-toggle" onclick="toggleMenu()">☰</span>
<h2>Faran Institute of Technology</h2>
<a class="nav-btn" href="logout.php">Logout</a>
</div>

<div class="content">

<div class="main-card">
<h2>Welcome 👋</h2>
<p>Faran Institute of Technology provides quality education and modern learning environment.</p>
</div>

<div class="cards">

<div class="card">
<div>
<h3>Departments</h3>
<p>CIT, ET</p>
</div>
<div class="num"><?php echo $departmentCount; ?></div>
<div class="icon">🏢</div>
</div>

<div class="card">
<div>
<h3>Students</h3>
<p>Total Students</p>
</div>
<div class="num"><?php echo $studentCount; ?></div>
<div class="icon">🎓</div>
</div>

<div class="card">
<div>
<h3>Staff</h3>
<p>Total Staff Members</p>
</div>
<div class="num"><?php echo $staffCount; ?></div>
<div class="icon">👨‍🏫</div>
</div>

</div>

<!-- PRINCIPAL (UNCHANGED) -->
<!-- PRINCIPAL MESSAGE -->
<div class="principal-section">

    <!-- PC / DESKTOP VERSION -->
    <div class="principal-content desktop-msg">

        <h2>Principal Message</h2>

        <p>
            It is my great pleasure to welcome all students, parents, and visitors to our institution. 
            At FIT, we are committed to providing a high-quality education that combines strong academic knowledge with practical technical skills. 
            Our goal is to prepare students not only for examinations but also for real-world challenges and professional success.

           

            We believe that education is not just about learning facts, but about developing character, discipline, creativity, and confidence. 
            Our dedicated faculty works tirelessly to create a positive learning environment where every student can grow according to their potential.

          

            In today’s fast-changing technological world, we continuously update our teaching methods and resources to ensure that our students stay ahead in innovation and modern skills. 
            We encourage hard work, honesty, teamwork, and respect — values that shape successful individuals and responsible citizens.

          <br><br>

            I warmly invite you to be part of our journey of excellence, growth, and achievement.

            <br><br>

            <strong>Principal</strong><br>
            Faran Institute of Technology
        </p>

    </div>

    <!-- MOBILE VERSION -->
    <div class="principal-content mobile-msg">

        <h2>Principal Message</h2>

        <p>
            We focus on quality education, discipline, and practical skills to prepare students for real-world success. 
            Our goal is to build confident, skilled, and responsible individuals for the future.

            <br><br>

            <strong>Principal</strong><br>
            Faran Institute of Technology
        </p>

    </div>

</div>

</div>

<!-- POPUP -->
<div class="popup" id="departmentPopup">
<div class="popup-content">

<span class="close-btn" onclick="closeDepartments()">×</span>

<h3>Departments</h3>

<a href="cit_dashboard.php">📘 CIT Department</a>
<a href="et_dashboard.php">⚙️ ET Department</a>

</div>
</div>

<!-- MAP POPUP -->
<div class="map-popup" id="mapPopup">

<div class="map-box">

<span class="close-map" onclick="closeLocation()">×</span>

<h2>Our Live Location(Gujrat)</h2>



<a 
href="https://maps.app.goo.gl/1F2GFXNUWxtRpzL37"
target="_blank"
class="open-map-btn">

Open in Google Maps

</a>

</div>

</div>

<script>

function toggleMenu(){

let sidebar = document.getElementById("sidebar");
let overlay = document.getElementById("overlay");

sidebar.classList.toggle("active");

if(sidebar.classList.contains("active")){
overlay.style.display = "block";
}else{
overlay.style.display = "none";
}

}

function openDepartments(){
document.getElementById("departmentPopup").style.display="flex";
}

function closeDepartments(){
document.getElementById("departmentPopup").style.display="none";
}

/* =========================
   NOTICE BADGE
========================= */
function updateNoticeBadge(){

fetch("get_notice_count.php")
.then(res => res.json())
.then(data => {

let badge = document.getElementById("noticeBadge");

if(!badge) return;

if(data.count > 0){
badge.style.display = "inline-block";
badge.innerText = data.count;
} else {
badge.style.display = "none";
}

})
.catch(err => {
console.log("Badge error:", err);
});

}

/* FIRST LOAD */
updateNoticeBadge();

/* LIVE UPDATE EVERY 3 SECONDS */
setInterval(updateNoticeBadge, 3000);

/* =========================
   LIVE NOTICE SCROLL
========================= */
function loadLatestNotice(){

fetch("latest_notice.php")
.then(res => res.json())
.then(data => {

let box = document.getElementById("noticeText");

if(!box) return;

box.innerText = data.text ?? "No notice available";

})
.catch(err => {

console.log("Notice error:", err);

let box = document.getElementById("noticeText");
if(box){
box.innerText = "Notice failed to load";
}

});

}

/* FIRST LOAD */
loadLatestNotice();

/* AUTO UPDATE EVERY 5 SECONDS */
setInterval(loadLatestNotice, 5000);

/* =========================
   OVERLAY CLOSE FIX
========================= */
document.getElementById("overlay").addEventListener("click", function(){

let sidebar = document.getElementById("sidebar");

sidebar.classList.remove("active");
this.style.display = "none";

});

/* OPEN MAP */
function openLocation(){

document.getElementById("mapPopup").style.display = "flex";

}

/* CLOSE MAP */
function closeLocation(){

document.getElementById("mapPopup").style.display = "none";

}

/* CLOSE WHEN CLICK OUTSIDE */
window.onclick = function(event){

let popup = document.getElementById("mapPopup");

if(event.target == popup){

popup.style.display = "none";

}

}

</script>

</body>
</html>
