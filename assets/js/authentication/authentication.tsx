import React from 'react';
import ReactDOM from 'react-dom';
import QrCode from "./components/qrcode";

declare const image: string;

ReactDOM.render(
    <QrCode
        image={image}
    />,
    document.getElementById('root')
);
