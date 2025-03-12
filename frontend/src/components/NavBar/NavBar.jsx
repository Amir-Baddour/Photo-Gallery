import React from 'react';
import './NavBar.css';

const NavBar = () => {
  const fullname = localStorage.getItem('fullname') || 'User';

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user_id');
    localStorage.removeItem('fullname'); 
    window.location.href = '/login';
  };

  return (
    <nav className="navbar">
      <div className="navbar-welcome">
        Welcome, <span className="navbar-username">{fullname}</span>
      </div>
      <button className="logout-button" onClick={handleLogout}>Logout</button>
    </nav>
  );
};

export default NavBar;
