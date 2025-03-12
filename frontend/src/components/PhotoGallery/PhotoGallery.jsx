import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import PhotoCard from '../PhotoCard/PhotoCard';
import './PhotoGallery.css';

const PhotoGallery = ({ onEditPhoto, onDeletePhoto }) => {
  const [photos, setPhotos] = useState([]);

  const fetchPhotos = async () => {
    try {
      const response = await api.get('/index.php?api=photo&action=getAll');
      if (response.data.success) {
        setPhotos(response.data.photos);
      }
    } catch (err) {
      console.error("Error fetching photos", err);
    }
  };

  useEffect(() => {
    fetchPhotos();
  }, []);

  return (
    <div className="photo-gallery">
      {photos.map(photo => (
        <PhotoCard
          key={photo.id}
          photo={photo}
          onEdit={onEditPhoto}
          onDelete={onDeletePhoto}
        />
      ))}
    </div>
  );
};

export default PhotoGallery;
