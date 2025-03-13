import React, { useState } from 'react';
import api from '../../services/api'; 
import './RegisterForm.css'; 

const RegisterForm = ({ onRegisterSuccess }) => {
  // State variables for form inputs
  const [fullname, setFullname] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [message, setMessage] = useState('');

  // Handle form submission
  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      // Send registration request to the API
      const response = await api.post('/index.php?api=user&action=register', { fullname, email, password });

      if (response.data.success) {
        setMessage('Registration successful! Please log in.'); 
        onRegisterSuccess(); 
      } else {
        setError(response.data.message); 
      }
    } catch (err) {
      console.error(err);
      setError('An error occurred. Please try again.');
    }
  };

  return (
    <div className="register-form-container">
      <h2 className="register-title">Register</h2>
      {error && <p className="error-msg">{error}</p>} 
      {message && <p className="success-msg">{message}</p>} 
      <form onSubmit={handleSubmit} className="register-form">
        <input
          type="text"
          placeholder="Full Name"
          value={fullname}
          onChange={(e) => setFullname(e.target.value)}
          required
        />
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
        <button type="submit" className="register-button">Register</button> 
      </form>
      <div className="login-link">
        Already have an account? <a href="/">Login here</a> 
      </div>
    </div>
  );
};

export default RegisterForm;
