// frontend/src/pages/LoginPage.jsx
import React from 'react';
import LoginForm from '../../components/LoginForm/LoginForm';
import './LoginPage.css';

const LoginPage = () => {
  const handleLoginSuccess = () => {
    window.location.href = '/';
  };

  return (
    <div className="login-page-container">
      <LoginForm onLoginSuccess={handleLoginSuccess} />
    </div>
  );
};

export default LoginPage;
