import React from 'react';
import RegisterForm from '../../components/RegisterForm/RegisterForm';
import './RegisterPage.css';

const RegisterPage = () => {
  const handleRegisterSuccess = () => {
    window.location.href = '/login';
  };

  return (
    <div className="register-page-container">
      <RegisterForm onRegisterSuccess={handleRegisterSuccess} />
    </div>
  );
};

export default RegisterPage;
