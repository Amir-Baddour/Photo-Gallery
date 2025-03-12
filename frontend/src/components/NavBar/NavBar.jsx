import React from 'react';
import './NavBar.css';

const NavBar = () => {
  const handleLogout = () => {
    localStorage.removeItem('token');
    window.location.href = '/login';
  };

  return (
    <nav className="navbar">
      <div className="navbar-title">Photo Gallery App</div>
      <button className="logout-button" onClick={handleLogout}>Logout</button>
    </nav>
  );
};

export default NavBar;
