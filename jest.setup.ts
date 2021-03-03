import "jest-extended";
import "@testing-library/react";
import "@testing-library/jest-dom";
import "@testing-library/jest-dom/extend-expect";

global.window = Object.create(window);
Object.defineProperty(window, 'location', {
  value: {
    ...window.location,
  },
  writable: true,
});
