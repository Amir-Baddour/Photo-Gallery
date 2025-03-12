import React, { useState, useEffect, useMemo } from 'react';
import NavBar from '../../components/NavBar/NavBar';
import GalleryHeader from '../../components/GalleryHeader/GalleryHeader';
import PhotoGallery from '../../components/PhotoGallery/PhotoGallery';
import PhotoForm from '../../components/PhotoForm/PhotoForm';
import Modal from '../../components/Modal/Modal';
import api from '../../services/api';
import './HomePage.css';

const HomePage = () => {
  const [photos, setPhotos] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedTag, setSelectedTag] = useState('');
  const [showForm, setShowForm] = useState(false);
  const [editPhoto, setEditPhoto] = useState(null);

  // Fetch all photos once
  const fetchPhotos = async () => {
    try {
      const response = await api.get('/index.php?api=photo&action=getAll');
      if (response.data.success) {
        setPhotos(response.data.photos);
      }
    } catch (error) {
      console.error("Error fetching photos:", error);
    }
  };

  useEffect(() => {
    fetchPhotos();
  }, []);

  // Compute unique tags from photos
  const uniqueTags = useMemo(() => {
    const allTags = photos.reduce((acc, photo) => {
      if (photo.tags) {
        const tagsArr = photo.tags.split(' ').map(tag => tag.trim());
        return acc.concat(tagsArr);
      }
      return acc;
    }, []);
    return [...new Set(allTags)];
  }, [photos]);

  // Filter logic
  const filteredPhotos = useMemo(() => {
    return photos.filter((photo) => {
      const lowerSearch = searchQuery.toLowerCase();
      const matchesSearch =
        photo.title.toLowerCase().includes(lowerSearch) ||
        photo.description.toLowerCase().includes(lowerSearch) ||
        photo.tags.toLowerCase().includes(lowerSearch);

      const matchesTag = selectedTag
        ? photo.tags.toLowerCase().includes(selectedTag.toLowerCase())
        : true;

      return matchesSearch && matchesTag;
    });
  }, [photos, searchQuery, selectedTag]);

  // Handlers
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
          fetchPhotos();
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
    fetchPhotos(); 
  };

  const handleCancelForm = () => {
    setShowForm(false);
  };

  return (
    <div className="home-page">
      <NavBar />
      <div className="home-content">
        
        {/* The combined header with search + filter + add button */}
        <GalleryHeader
          title="Your Photos"
          searchQuery={searchQuery}
          setSearchQuery={setSearchQuery}
          selectedTag={selectedTag}
          setSelectedTag={setSelectedTag}
          uniqueTags={uniqueTags}
          onAddPhoto={handleAddPhoto}
        />
        
        <PhotoGallery
          photos={filteredPhotos}
          onEditPhoto={handleEditPhoto}
          onDeletePhoto={handleDeletePhoto}
        />

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
