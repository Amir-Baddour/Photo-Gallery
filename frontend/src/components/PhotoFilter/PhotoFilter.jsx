import React from 'react';
import './PhotoFilter.css';

const PhotoFilter = ({ searchQuery, setSearchQuery, selectedTag, setSelectedTag, uniqueTags }) => {
  return (
    <div className="photo-filter-container">
      <input
        type="text"
        placeholder="Search photos..."
        value={searchQuery}
        onChange={(e) => setSearchQuery(e.target.value)}
        className="filter-search"
      />
      <select
        value={selectedTag}
        onChange={(e) => setSelectedTag(e.target.value)}
        className="filter-select"
      >
        <option value="">All Tags</option>
        {uniqueTags.map((tag, index) => (
          <option key={index} value={tag}>{tag}</option>
        ))}
      </select>
    </div>
  );
};

export default PhotoFilter;
