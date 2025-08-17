<body>
  <div class="container">
    <div class="header-text">
      <img class="header-text-logo" src="{{ asset('static/logo.png') }}" alt="logo">
      <p class="header-text-description">Student Login</p>
      <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    </div>
    <form method="POST" action="{{ route('login.submit') }}">
      @csrf
      <input type="text" 
             placeholder="Email"
             name="email"
             id="email"
             autofocus style="margin-bottom: 5px">
      <input type="password" 
             name="password"
             id="password" 
             placeholder="Password"
             required>
      <button type="submit" name="login">Login</button>
    </form>
   </div>
</body>