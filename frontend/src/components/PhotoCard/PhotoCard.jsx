// frontend/src/components/PhotoCard.jsx

import React from 'react';
import './PhotoCard.css';

const PhotoCard = ({ photo, onEdit, onDelete }) => {
  const BASE_URL = 'http://localhost/photo-gallery-app/backend/';

  return (
    <div className="photo-card">
      <img
        className="photo-image"
        src={`${BASE_URL}${photo.image_path}`}
        alt={photo.title}
      />
      <div className="photo-info">
        <h3 className="photo-title">{photo.title}</h3>
        <p className="photo-description">{photo.description}</p>
        <p className="photo-tags"><strong>Tags:</strong> {photo.tags}</p>
      </div>
      <div className="photo-actions">
        <button onClick={() => onEdit(photo)}>Edit</button>
        <button onClick={() => onDelete(photo.id)}>Delete</button>
      </div>
    </div>
  );
};

export default PhotoCard;
