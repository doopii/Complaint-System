<!DOCTYPE html>
<html>
<head>
    <title>Submit Complaint</title>
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/student-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/complaint-form.css') }}">
</head>
<body>
    <!-- Site Header -->
    <header class="navbar">
        <div class="navbar-inner">
            <div class="navbar-left">
                <a href="{{ route('home') }}" class="logo">FixIt</a>
                <nav class="navbar-center">
                    <a href="{{ route('home') }}">Home</a>
                    @auth
                        @if(auth()->user()->isStudent())
                            <a href="{{ route('student.community') }}">Community</a>
                            <a href="{{ route('complaints.dashboard') }}">My Complaints</a>
                            <a href="{{ route('student.profile') }}">Profile</a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('complaints.dashboard', ['student_id' => $studentId ?? '']) }}" class="active">Dashboard</a>
                        <a href="{{ route('complaints.create', ['student_id' => $studentId ?? '']) }}">Submit Complaint</a>
                    @endauth
                </nav>
            </div>
            <div class="navbar-right">
                @auth
                    <span class="user-info">{{ auth()->user()->name ?? auth()->user()->email }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn logout-btn">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="form-container">
        <h2>Submit a New Complaint</h2>
        
        <!-- Complaint Submission Form -->
        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @auth
                @if(auth()->user()->isStudent())
                    <!-- Student ID display for authenticated users -->
                    <div class="form-group">
                        <label>Student ID</label>
                        <div class="student-id-display">{{ auth()->user()->student_id }}</div>
                        <div class="form-helper">This is automatically filled from your account</div>
                    </div>
                @endif
            @else
                <!-- For backward compatibility with non-authenticated users -->
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" name="student_id" id="student_id" value="{{ $studentId ?? '' }}" required>
                </div>
            @endauth

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" required>
                    <option value="">-- Select Category --</option>
                    <option value="Academic">Academic</option>
                    <option value="Facilities">Facilities</option>
                    <option value="IT">IT Services</option>
                    <option value="Security">Security</option>
                    <option value="Food Services">Food Services</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Other">Other</option>
                </select>
                <div class="form-helper">Please select the most relevant category.</div>
            </div>

            <div class="form-group">
                <label for="priority">Priority Level</label>
                <select name="priority" id="priority" required>
                    <option value="">-- Select Priority --</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
                <div class="form-helper">How urgent is this complaint?</div>
            </div>

            <div class="form-group">
                <label for="photo">Photo (optional)</label>
                <!-- Custom file input -->
                <label class="file-label" for="photo">Choose File</label>
                <input type="file" name="photo" id="photo" accept="image/*" style="display: none;" onchange="updateFileName()">
                <span class="selected-file" id="selectedFile">No file chosen</span>
                <div class="form-helper">Accepted formats: JPG, PNG, GIF.</div>
            </div>

            <div class="form-group">
                <button type="submit">Submit Complaint</button>
            </div>
        </form>

        <div class="form-footer">
            <a href="{{ route('complaints.dashboard') }}">&larr; Back to Dashboard</a>
        </div>
    </div>

    <script>
    function updateFileName() {
        var input = document.getElementById('photo');
        var fileLabel = document.getElementById('selectedFile');
        if (input.files.length > 0) {
            fileLabel.textContent = input.files[0].name;
        } else {
            fileLabel.textContent = "No file chosen";
        }
    }
    </script>
</body>
</html>
