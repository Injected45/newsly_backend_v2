import React from 'react';
import { StatusBar } from 'react-native';
import { ThemeProvider } from './src/contexts/ThemeContext';
import { AuthProvider } from './src/contexts/AuthContext';
import RootNavigator from './src/navigation/RootNavigator';

const App = () => {
  return (
    <ThemeProvider>
      <AuthProvider>
        <StatusBar barStyle="light-content" />
        <RootNavigator />
      </AuthProvider>
    </ThemeProvider>
  );
};

export default App;



