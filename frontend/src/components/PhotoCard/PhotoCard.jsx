import React, { useState } from 'react';
import './PhotoCard.css';

const PhotoCard = ({ photo, onEdit, onDelete }) => {
  const [showMenu, setShowMenu] = useState(false);

  const BASE_URL = 'http://localhost/photo-gallery-app/backend/';

  const handleMenuToggle = (e) => {
    e.stopPropagation(); // Prevent card click from closing the menu if needed
    setShowMenu(!showMenu);
  };

  const handleEdit = () => {
    setShowMenu(false);
    onEdit(photo);
  };

  const handleDelete = () => {
    setShowMenu(false);
    onDelete(photo.id);
  };

  return (
    <div className="photo-card">
      {/* Image */}
      <img
        className="photo-image"
        src={`${BASE_URL}${photo.image_path}`}
        alt={photo.title}
      />

      {/* Overlay with title & description */}
      <div className="photo-overlay">
        <div className="photo-text">
          <h3 className="photo-title">{photo.title}</h3>
          <p className="photo-description">{photo.description}</p>
          <p className="photo-tags">{photo.tags}</p>
        </div>
      </div>

      {/* Three-dot menu button (top-right) */}
      <div className="menu-button" onClick={handleMenuToggle}>•••</div>

      {/* Dropdown menu for Edit & Delete */}
      {showMenu && (
        <div className="dropdown-menu" onClick={(e) => e.stopPropagation()}>
          <div className="dropdown-item" onClick={handleEdit}>Edit</div>
          <div className="dropdown-item" onClick={handleDelete}>Delete</div>
        </div>
      )}
    </div>
  );
};

export default PhotoCard;
