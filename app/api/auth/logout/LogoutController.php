<?php
function logout($db_connect) {
  try {
      $headers = getallheaders();
      $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

      if (!$token) {
          sendResponse(
              success: false,
              code: 401,
              message: 'No token provided'
          );
          return;
      }

      sendResponse(
          success: true,
          code: 200,
          message: 'Logout successful'
      );

  } catch (Exception $e) {
      sendResponse(
          success: false,
          code: 500,
          message: 'Logout failed: ' . $e->getMessage()
      );
  }
}