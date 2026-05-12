<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>FIT Login</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
margin:0;
font-family:Arial;
background:#f4f6fb;
}

.top-header{
background:#0a2a66;
color:white;
text-align:center;
padding:20px;
}

.top-header img{
width:60px;
height:60px;
border-radius:50%;
border:2px solid white;
}

/* MAIN PC FIX */
.main{
display:flex;
justify-content:center;   /* center form on PC */
padding:30px;
gap:20px;
}

/* REMOVE SIDE ON PC */
.side{
display:none;
}

.center{
width:100%;
display:flex;
justify-content:center;
align-items:center;
}

.login-box{
background:white;
padding:30px;
width:320px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
text-align:center;
}

input{
width:100%;
padding:10px;
margin:8px 0;
border:1px solid #ccc;
border-radius:6px;
}

button{
width:100%;
padding:10px;
background:#0a2a66;
color:white;
border:none;
border-radius:6px;
cursor:pointer;
font-weight:bold;
}

.msg{
padding:10px;
margin-bottom:10px;
border-radius:6px;
font-size:14px;
}

.error{background:#ffdddd;color:#a10000;}
.success{background:#ddffdd;color:#0a6b0a;}

/* LINKS */
.links{
margin-top:12px;
text-align:center;
}

.links a{
display:block;
margin-top:6px;
text-decoration:none;
font-weight:bold;
color:#0a2a66;
}

/* MOBILE (UNCHANGED) */
@media(max-width:900px){

.side{display:none;}

.main{
flex-direction:column;
padding:15px;
}

.center{
width:100%;
}

.login-box{
width:100%;
max-width:420px;
}
}
</style>
</head>

<body>

<div class="top-header">
<img src="img/fit.jpeg">
<h1>Faran Institute of Technology (FIT)</h1>
</div>

<div class="main">

<div class="side">
<h3>About FIT</h3>
<p>FIT offers CIT and ET departments...</p>
</div>

<div class="center">

<div class="login-box">

<h2>User Login</h2>

<!-- OTP NOTICE -->
<p style="font-size:13px;color:#0a2a66;margin-bottom:10px;">
Login requires email verification (OTP). Please verify your email after registration.
</p>

<?php if(isset($_SESSION['error'])) { ?>
<div class="msg error msgBox">
<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
</div>
<?php } ?>

<?php if(isset($_SESSION['success'])) { ?>
<div class="msg success msgBox">
<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<?php } ?>

<form action="login_process.php" method="POST">

<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>

<button type="submit" name="login">Login</button>

</form>

<div class="links">
<a href="forgot.php">Forgot Password</a>
<a href="register.php">Don't have an account? Register</a>
</div>

</div>

</div>

<div class="side">
<h3>Vision</h3>
<p>Center of excellence in technical education...</p>
</div>

</div>

<script>
setTimeout(() => {
    document.querySelectorAll(".msgBox").forEach(el => {
        el.style.transition = "0.5s";
        el.style.opacity = "0";
        setTimeout(() => el.remove(), 500);
    });
}, 3000);
</script>

</body>
</html>
