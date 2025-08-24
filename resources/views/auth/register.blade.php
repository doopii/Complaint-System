<!DOCTYPE html>
<html>
<head>
    <title>Register - FixIt</title>
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/auth-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
</head>
<body>
    <!-- Site Header -->
    <header class="navbar">
        <div class="navbar-inner">
            <div class="navbar-left">
                <a href="{{ route('home') }}" class="logo">FixIt</a>
            </div>
            <nav class="navbar-center">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}" class="active">Register</a>
            </nav>
            <div class="navbar-right">
            </div>
        </div>
    </header>

    <div class="form-container">
        <h2>Create Your Account</h2>
        
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="complaint-form">
            @csrf
            
            <!-- Account Type Selector at Top -->
            <div class="account-type-selector">
                <h3>Choose Your Role</h3>
                <div class="role-buttons">
                    <div class="role-card" data-role="student">
                        <div class="role-title">Student</div>
                        <div class="role-desc">Submit and track complaints</div>
                    </div>
                    <div class="role-card" data-role="admin">
                        <div class="role-title">Admin</div>
                        <div class="role-desc">Manage and resolve complaints</div>
                    </div>
                </div>
                <input type="hidden" id="account_type" name="account_type" value="{{ old('account_type') }}" required>
            </div>
            
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <!-- Student-specific fields -->
            <div id="student-fields" style="display: none;">
                <div class="form-group">
                    <label for="student_id">Student ID:</label>
                    <input type="text" id="student_id" name="student_id" value="{{ old('student_id') }}" placeholder="Enter your student ID">
                </div>

                <div class="form-group">
                    <label for="course">Course:</label>
                    <select id="course" name="course">
                        <option value="">Select Course</option>
                        <option value="Computer Science" {{ old('course') == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                        <option value="Information Technology" {{ old('course') == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                        <option value="Business Administration" {{ old('course') == 'Business Administration' ? 'selected' : '' }}>Business Administration</option>
                        <option value="Accounting" {{ old('course') == 'Accounting' ? 'selected' : '' }}>Accounting</option>
                        <option value="Engineering" {{ old('course') == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                        <option value="Education" {{ old('course') == 'Education' ? 'selected' : '' }}>Education</option>
                        <option value="Nursing" {{ old('course') == 'Nursing' ? 'selected' : '' }}>Nursing</option>
                        <option value="Psychology" {{ old('course') == 'Psychology' ? 'selected' : '' }}>Psychology</option>
                        <option value="Marketing" {{ old('course') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="Finance" {{ old('course') == 'Finance' ? 'selected' : '' }}>Finance</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="year_level">Year Level:</label>
                    <select id="year_level" name="year_level">
                        <option value="">Select Year Level</option>
                        <option value="1" {{ old('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ old('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ old('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ old('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                    </select>
                </div>
            </div>

            <!-- Admin-specific fields -->
            <div id="admin-fields" style="display: none;">
                <div class="form-group">
                    <label for="department">Department:</label>
                    <select id="department" name="department">
                        <option value="">Select Department</option>
                        <option value="Student Affairs" {{ old('department') == 'Student Affairs' ? 'selected' : '' }}>Student Affairs</option>
                        <option value="Academic Affairs" {{ old('department') == 'Academic Affairs' ? 'selected' : '' }}>Academic Affairs</option>
                        <option value="IT Services" {{ old('department') == 'IT Services' ? 'selected' : '' }}>IT Services</option>
                        <option value="Facilities" {{ old('department') == 'Facilities' ? 'selected' : '' }}>Facilities</option>
                        <option value="Security" {{ old('department') == 'Security' ? 'selected' : '' }}>Security</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="position">Position:</label>
                    <select id="position" name="position">
                        <option value="">Select Position</option>
                        <option value="Administrator" {{ old('position') == 'Administrator' ? 'selected' : '' }}>Administrator</option>
                        <option value="Manager" {{ old('position') == 'Manager' ? 'selected' : '' }}>Manager</option>
                        <option value="Officer" {{ old('position') == 'Officer' ? 'selected' : '' }}>Officer</option>
                        <option value="Staff" {{ old('position') == 'Staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>
            </div>

            <!-- Common fields -->
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="submit-btn">Register</button>
        </form>

        <div class="auth-links">
            <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        </div>
    </div>

    <script>
        // Role card selection
        document.addEventListener('DOMContentLoaded', function() {
            const roleCards = document.querySelectorAll('.role-card');
            const accountTypeInput = document.getElementById('account_type');
            const studentFields = document.getElementById('student-fields');
            const adminFields = document.getElementById('admin-fields');
            const studentIdField = document.getElementById('student_id');
            const courseField = document.getElementById('course');
            const yearLevelField = document.getElementById('year_level');
            const departmentField = document.getElementById('department');
            const positionField = document.getElementById('position');

            // Handle role card clicks
            roleCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove active class from all cards
                    roleCards.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked card
                    this.classList.add('active');
                    
                    // Set the account type value
                    const roleType = this.getAttribute('data-role');
                    accountTypeInput.value = roleType;
                    
                    // Trigger field visibility changes
                    handleRoleChange(roleType);
                });
            });

            // Handle role change logic
            function handleRoleChange(roleType) {
                if (roleType === 'student') {
                    studentFields.style.display = 'block';
                    adminFields.style.display = 'none';
                    
                    // Make student fields required
                    studentIdField.required = true;
                    courseField.required = true;
                    yearLevelField.required = true;
                    departmentField.required = false;
                    positionField.required = false;
                } else if (roleType === 'admin') {
                    studentFields.style.display = 'none';
                    adminFields.style.display = 'block';
                    
                    // Make admin fields required
                    studentIdField.required = false;
                    courseField.required = false;
                    yearLevelField.required = false;
                    departmentField.required = true;
                    positionField.required = true;
                } else {
                    studentFields.style.display = 'none';
                    adminFields.style.display = 'none';
                    
                    // Clear all requirements
                    studentIdField.required = false;
                    courseField.required = false;
                    yearLevelField.required = false;
                    departmentField.required = false;
                    positionField.required = false;
                }
            }

            // Set initial state if account type is already selected (on validation errors)
            const initialAccountType = accountTypeInput.value;
            if (initialAccountType) {
                const initialCard = document.querySelector(`[data-role="${initialAccountType}"]`);
                if (initialCard) {
                    initialCard.classList.add('active');
                }
                handleRoleChange(initialAccountType);
            }
        });
    </script>
</body>
</html>
