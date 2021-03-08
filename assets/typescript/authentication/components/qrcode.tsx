import React from 'react';
import Image from 'react-bootstrap/Image';

export type Props = {
  image: string;
};

const QrCode: React.FC<Props> = ({ image }: Props) => (
  <div className="m-2 p-2">
    <Image src={image} thumbnail />
  </div>
);

export default QrCode;
