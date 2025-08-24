<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/student-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/profile.css') }}">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="navbar-inner">
            <div class="navbar-left">
                <a href="{{ route('home') }}" class="logo">FixIt</a>
            </div>
            <nav class="navbar-center">
                <a href="{{ route('home') }}">Home</a>
                @auth
                    @if(auth()->user()->isStudent())
                        <a href="{{ route('student.community') }}">Community</a>
                        <a href="{{ route('complaints.dashboard') }}">My Complaints</a>
                        <a href="{{ route('student.profile') }}" class="active">Profile</a>
                    @endif
                @endauth
            </nav>
            <div class="navbar-right">
                @auth
                    <span class="user-info">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button class="logout-btn" type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn">Login</a>
                    <a href="{{ route('register') }}" class="btn">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="form-container" style="max-width:1100px;">
        <header class="profile-header">
            <h1 class="page-title">Student Profile</h1>
            <p class="page-subtitle">View and update your details</p>
        </header>

        @if (session('status'))
            <div class="success-message">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-message">
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="profile-content">
            <!-- Left -->
            <aside class="profile-picture-section profile-card">
                @php
                    $name = $student->name ?? 'Student';
                    $initials = collect(explode(' ', trim($name)))
                        ->filter()
                        ->map(fn($p) => mb_substr($p, 0, 1))
                        ->take(2)
                        ->implode('');
                    $avatar = $student->profile_picture_url ?? null;
                @endphp

                <div class="profile-picture-container">
                    @if ($avatar)
                        <img class="profile-picture" src="{{ $avatar }}" alt="Profile photo of {{ $name }}">
                    @else
                        <div class="profile-picture-placeholder">
                            <span class="profile-initials">{{ $initials }}</span>
                        </div>
                    @endif
                </div>

                <div class="profile-info-preview">
                    <h2 class="profile-name">{{ $student->name }}</h2>
                    <p class="profile-id">Student ID: {{ $student->student_id }}</p>
                    <p class="profile-email">{{ $student->email }}</p>
                </div>
            </aside>

            <!-- Right -->
            <section class="profile-form-section profile-card">
                <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="profile-form">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <h3 class="section-title">Profile Picture</h3>
                        <div class="form-group">
                            <label for="profile_picture">Choose Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="file-input">
                            <p class="help-text">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="section-title">Personal Information</h3>

                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name', $student->name) }}" required maxlength="255">
                        </div>

                        <div class="form-group">
                            <label for="course">Course</label>
                            <select name="course" id="course" required>
                                <option value="">Select Course</option>
                                <option value="Computer Science" {{ old('course', $student->course) == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                <option value="Information Technology" {{ old('course', $student->course) == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                                <option value="Business Administration" {{ old('course', $student->course) == 'Business Administration' ? 'selected' : '' }}>Business Administration</option>
                                <option value="Accounting" {{ old('course', $student->course) == 'Accounting' ? 'selected' : '' }}>Accounting</option>
                                <option value="Engineering" {{ old('course', $student->course) == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                <option value="Education" {{ old('course', $student->course) == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Nursing" {{ old('course', $student->course) == 'Nursing' ? 'selected' : '' }}>Nursing</option>
                                <option value="Psychology" {{ old('course', $student->course) == 'Psychology' ? 'selected' : '' }}>Psychology</option>
                                <option value="Marketing" {{ old('course', $student->course) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Finance" {{ old('course', $student->course) == 'Finance' ? 'selected' : '' }}>Finance</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="year_level">Year Level</label>
                            <select name="year_level" id="year_level" required>
                                <option value="">Select Year</option>
                                @for($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ (int)old('year_level', $student->year_level) === $i ? 'selected' : '' }}>
                                        Year {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="bio">About Me</label>
                            <textarea name="bio" id="bio" maxlength="1000"
                                      placeholder="Tell us about yourself...">{{ old('bio', $student->bio) }}</textarea>
                            <p class="help-text" id="bio-help">Maximum 1000 characters</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Profile</button>
                        <a href="{{ route('complaints.dashboard') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
    // Preview uploaded photo
    document.getElementById('profile_picture')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(evt) {
            const img = document.querySelector('.profile-picture');
            const placeholder = document.querySelector('.profile-picture-placeholder');
            if (img) {
                img.src = evt.target.result;
            } else if (placeholder) {
                const newImg = document.createElement('img');
                newImg.className = 'profile-picture';
                newImg.src = evt.target.result;
                placeholder.parentNode.replaceChild(newImg, placeholder);
            }
        };
        reader.readAsDataURL(file);
    });

    // Bio character counter
    const bio = document.getElementById('bio');
    const help = document.getElementById('bio-help');
    if (bio && help) {
        const maxLen = parseInt(bio.getAttribute('maxlength') || '1000', 10);
        const update = () => {
            const remaining = maxLen - bio.value.length;
            help.textContent = `${remaining} characters remaining`;
            help.style.color = remaining < 0 ? '#dc3545' :
                               remaining < 100 ? '#fd7e14' :
                               'var(--form-helper)';
        };
        bio.addEventListener('input', update);
        update();
    }
    </script>
</body>
</html>
