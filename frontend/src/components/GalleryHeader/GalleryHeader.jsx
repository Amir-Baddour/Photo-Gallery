import React from 'react';
import './GalleryHeader.css';

const GalleryHeader = ({
  title = 'Your Photos',
  searchQuery,
  setSearchQuery,
  selectedTag,
  setSelectedTag,
  uniqueTags,
  onAddPhoto
}) => {
  return (
    <div className="gallery-header">
      <h1 className="gallery-header-title">{title}</h1>
      
      <div className="gallery-filter">
        <input
          type="text"
          placeholder="Search photos..."
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          className="gallery-filter-input"
        />
        <select
          value={selectedTag}
          onChange={(e) => setSelectedTag(e.target.value)}
          className="gallery-filter-select"
        >
          <option value="">All Tags</option>
          {uniqueTags.map((tag, idx) => (
            <option key={idx} value={tag}>{tag}</option>
          ))}
        </select>
      </div>
      
      <button className="add-photo-button" onClick={onAddPhoto}>Add Photo</button>
    </div>
  );
};

export default GalleryHeader;
