<?php
session_start();
require_once "config/database.php";
require_once "classes/User.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';
$error = '';


// Get current user details
$user_details = $user->getUserDetails($_SESSION['user_id']);


// Handle profile picture upload
if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $upload_dir = "uploads/profile_pictures/";
    
    // Create directory if it doesn't exist
    if(!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['profile_picture'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    
    // Validate file
    if($file_error === 0) {
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if(in_array($file_ext, $allowed_extensions)) {
            if($file_size < 5000000) { // 5MB limit
                $new_file_name = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_ext;
                $file_path = $upload_dir . $new_file_name;
                
                if(move_uploaded_file($file_tmp, $file_path)) {
                    // Update database
                    if($user->updateProfilePicture($_SESSION['user_id'], $file_path)) {
                        $message = "Profile picture updated successfully!";
                        
                        // Delete old profile picture if exists
                        if($user_details['profile_picture'] && file_exists($user_details['profile_picture'])) {
                            unlink($user_details['profile_picture']);
                        }
                        
                        // Refresh user details
                        $user_details = $user->getUserDetails($_SESSION['user_id']);
                    } else {
                        $error = "Failed to update profile picture in database.";
                        unlink($file_path); // Remove uploaded file
                    }
                } else {
                    $error = "Failed to upload file.";
                }
            } else {
                $error = "File size too large. Maximum size is 5MB.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        $error = "Error uploading file.";
    }
}

// Handle profile picture removal
if($_POST && isset($_POST['remove_picture'])) {
    if($user_details['profile_picture'] && file_exists($user_details['profile_picture'])) {
        unlink($user_details['profile_picture']);
    }
    
    if($user->updateProfilePicture($_SESSION['user_id'], null)) {
        $message = "Profile picture removed successfully!";
        $user_details = $user->getUserDetails($_SESSION['user_id']);
    } else {
        $error = "Failed to remove profile picture.";
    }
}
?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="profile-page">
        <h1>Profile Picture</h1>
        
        <?php if($message): ?>
            <div class="success-message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        
        <div class="profile-section">
            <div class="current-picture">
                <h3>Current Profile Picture</h3>
                <div class="picture-container">
                    <?php if($user_details['profile_picture'] && file_exists($user_details['profile_picture'])): ?>
                        <img src="<?php echo $user_details['profile_picture']; ?>" alt="Profile Picture" class="profile-img">
                    <?php else: ?>
                        <div class="no-picture">
                            <span>👤</span>
                            <p>No profile picture</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if($user_details['profile_picture']): ?>
                    <form method="POST" style="margin-top: 1rem;">
                        <button type="submit" name="remove_picture" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove your profile picture?')">Remove Picture</button>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="upload-section">
                <h3>Upload New Picture</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_picture">Choose Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                        <small>Maximum file size: 5MB. Allowed formats: JPG, JPEG, PNG, GIF</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Upload Picture</button>
                </form>
            </div>
        </div>
        
        <div class="profile-info">
            <h3>Profile Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Name:</strong> <?php echo htmlspecialchars($user_details['name']); ?>
                </div>
                <div class="info-item">
                    <strong>Email:</strong> <?php echo htmlspecialchars($user_details['email']); ?>
                </div>
                <div class="info-item">
                    <strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user_details['created_at'])); ?>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="orders.php" class="btn btn-outline">View Orders</a>
        </div>
    </div>
</div>

<style>
    .profile-page {
        padding: 2rem 0;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .profile-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin: 2rem 0;
    }
    
    .current-picture, .upload-section {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .picture-container {
        text-align: center;
        margin: 1rem 0;
    }
    
    .profile-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--secondary);
    }
    
    .no-picture {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: #f8f9fa;
        border: 4px solid #ddd;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .no-picture span {
        font-size: 3rem;
        color: #666;
    }
    
    .no-picture p {
        margin-top: 0.5rem;
        color: #666;
        font-size: 0.9rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-group input[type="file"] {
        width: 100%;
        padding: 0.8rem;
        border: 2px dashed #ddd;
        border-radius: 4px;
        background: #f8f9fa;
    }
    
    .form-group small {
        display: block;
        margin-top: 0.5rem;
        color: #666;
        font-size: 0.8rem;
    }
    
    .btn {
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: var(--secondary);
        color: white;
    }
    
    .btn-primary:hover {
        background: #2980b9;
    }
    
    .btn-danger {
        background: var(--accent);
        color: white;
    }
    
    .btn-danger:hover {
        background: #c0392b;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #545b62;
    }
    
    .btn-outline {
        background: transparent;
        color: var(--secondary);
        border: 2px solid var(--secondary);
    }
    
    .btn-outline:hover {
        background: var(--secondary);
        color: white;
    }
    
    .profile-info {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin: 2rem 0;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .info-item {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 4px;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
        .profile-section {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<script>
// Form submission handling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[enctype="multipart/form-data"]');
    if(form) {
        form.addEventListener('submit', function(e) {
            // Check if file is selected
            const fileInput = document.getElementById('profile_picture');
            if(fileInput.files.length === 0) {
                alert('Please select a file to upload');
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading...';
        });
    }
});
</script>

<?php include 'footer.php'; ?>
