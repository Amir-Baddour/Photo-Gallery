import React from 'react';
import './PhotoCard.css';

const PhotoCard = ({ photo, onEdit, onDelete }) => {
  const BASE_URL = 'http://localhost/photo-gallery-app/backend/';

  return (
    <div className="photo-card">
      <div className="photo-card-image-wrapper">
        <img
          className="photo-image"
          src={`${BASE_URL}${photo.image_path}`}
          alt={photo.title}
        />
      </div>
      <div className="photo-card-content">
        <h3 className="photo-title">{photo.title}</h3>
        <p className="photo-description">{photo.description}</p>
        <p className="photo-tags">
          <strong>Tags:</strong> {photo.tags}
        </p>
      </div>
      <div className="photo-card-actions">
        <button className="edit-button" onClick={() => onEdit(photo)}>Edit</button>
        <button className="delete-button" onClick={() => onDelete(photo.id)}>Delete</button>
      </div>
    </div>
  );
};

export default PhotoCard;
