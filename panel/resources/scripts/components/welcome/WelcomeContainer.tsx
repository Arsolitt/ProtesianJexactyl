import React from 'react';
import { Link, NavLink } from 'react-router-dom';

const WelcomeContainer = () => {
    return (
        <div>
            WELCOME PAGE
            <br />
            <Link to={'/auth/login'}>Вход</Link>
            <br />
            <NavLink to={'/home'}>Home</NavLink>
        </div>
    );
};

export default WelcomeContainer;
