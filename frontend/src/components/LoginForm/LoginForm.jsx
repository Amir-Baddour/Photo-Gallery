import React, { useState } from 'react';
import api from '../../services/api';
import './LoginForm.css';

const LoginForm = ({ onLoginSuccess }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await api.post('/index.php?api=user&action=login', { email, password });
      if (response.data.success) {
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user_id', response.data.user_id);
        localStorage.setItem('fullname', response.data.fullname);

        onLoginSuccess();
      } else {
        setError(response.data.message);
      }
    } catch (err) {
      console.error(err);
      setError('An error occurred. Please try again.');
    }
  };

  return (
    <div className="login-form-container">
      <h2 className="login-title">Login</h2>
      {error && <p className="error-msg">{error}</p>}
      <form onSubmit={handleSubmit} className="login-form">
        <input
          type="email"
          placeholder="Email Address"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          required
        />
        <input
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          required
        />
        <button type="submit" className="login-button">Login</button>
      </form>
      <div className="register-link">
        Don’t have an account? <a href="/register">Create one</a>
      </div>
    </div>
  );
};

export default LoginForm;
