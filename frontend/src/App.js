import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import LoginPage from './pages/LoginPage/LoginPage';
import RegisterPage from './pages/RegisterPage/RegisterPage';
import HomePage from './pages/HomePage/HomePage';

function App() {
  return (
    <Router>
      <Routes>
        {/* Home route - for now, just a placeholder */}
        <Route path="/" element={<HomePage/>} />

        {/* Login route */}
        <Route path="/login" element={<LoginPage />} />

        {/* Register route */}
        <Route path="/register" element={<RegisterPage />} />
      </Routes>
    </Router>
  );
}

export default App;
