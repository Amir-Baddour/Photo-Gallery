import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import LoginPage from './pages/LoginPage/LoginPage';
import RegisterPage from './pages/RegisterPage/RegisterPage';

function App() {
  return (
    <Router>
      <Routes>
        {/* Home route - for now, just a placeholder */}
        <Route path="/" element={<h1>Home Page</h1>} />

        {/* Login route */}
        <Route path="/login" element={<LoginPage />} />

        {/* Register route */}
        <Route path="/register" element={<RegisterPage />} />
      </Routes>
    </Router>
  );
}

export default App;
