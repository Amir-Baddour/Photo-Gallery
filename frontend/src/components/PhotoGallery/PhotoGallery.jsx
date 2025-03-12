import React from 'react';
import PhotoCard from '../PhotoCard/PhotoCard';
import './PhotoGallery.css';

const PhotoGallery = ({ photos, onEditPhoto, onDeletePhoto }) => {
  return (
    <div className="photo-gallery">
      {photos.length > 0 ? (
        <div className="photo-cards-container">
          {photos.map(photo => (
            <PhotoCard
              key={photo.id}
              photo={photo}
              onEdit={onEditPhoto}
              onDelete={onDeletePhoto}
            />
          ))}
        </div>
      ) : (
        <div className="no-photos">No photos match your criteria.</div>
      )}
    </div>
  );
};

export default PhotoGallery;
