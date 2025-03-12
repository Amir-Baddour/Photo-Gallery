import React, { useState } from 'react';
import NavBar from '../../components/NavBar/NavBar';
import PhotoGallery from '../../components/PhotoGallery/PhotoGallery';
import PhotoForm from '../../components/PhotoForm/PhotoForm';
import Modal from '../../components/Modal/Modal';
import api from '../../services/api';
import './HomePage.css';

const HomePage = () => {
  const [showForm, setShowForm] = useState(false);
  const [editPhoto, setEditPhoto] = useState(null);

  const handleAddPhoto = () => {
    setEditPhoto(null);
    setShowForm(true);
  };

  const handleEditPhoto = (photo) => {
    setEditPhoto(photo);
    setShowForm(true);
  };

  const handleDeletePhoto = async (photoId) => {
    if (window.confirm("Are you sure you want to delete this photo?")) {
      try {
        const response = await api.get(`/index.php?api=photo&action=delete&id=${photoId}`);
        if (response.data.success) {
          window.location.reload();
        } else {
          alert(response.data.message);
        }
      } catch (error) {
        console.error("Error deleting photo:", error);
      }
    }
  };

  const handleFormSuccess = () => {
    setShowForm(false);
    window.location.reload();
  };

  const handleCancelForm = () => {
    setShowForm(false);
  };

  return (
    <div className="home-page">
      <NavBar />
      <div className="home-content">
        <div className="gallery-header">
          <h1>Your Photos</h1>
          <button className="add-photo-button" onClick={handleAddPhoto}>Add Photo</button>
        </div>
        <PhotoGallery onEditPhoto={handleEditPhoto} onDeletePhoto={handleDeletePhoto} />
        
        {showForm && (
          <Modal onClose={handleCancelForm}>
            <PhotoForm
              photo={editPhoto}
              onSuccess={handleFormSuccess}
              onCancel={handleCancelForm}
            />
          </Modal>
        )}
      </div>
    </div>
  );
};

export default HomePage;
