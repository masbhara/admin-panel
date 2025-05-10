export function useThemeClasses() {
  return {
    // Background classes
    bgPrimary: 'bg-background-primary',
    bgSecondary: 'bg-background-secondary',
    bgTertiary: 'bg-background-tertiary',
    bgCard: 'bg-background-card',
    bgElevated: 'bg-background-elevated',
    bgInput: 'bg-background-input',
    
    // Text classes
    textPrimary: 'text-text-primary',
    textSecondary: 'text-text-secondary',
    textTertiary: 'text-text-tertiary',
    textMuted: 'text-text-muted',
    textHighlight: 'text-text-highlight',
    
    // Border classes
    borderLight: 'border-border-light',
    borderDefault: 'border-border-DEFAULT',
    borderDark: 'border-border-dark',
    borderFocus: 'border-border-focus',
    borderDivider: 'border-border-divider',
    
    // Status classes
    statusSuccess: 'text-status-success',
    statusWarning: 'text-status-warning',
    statusDanger: 'text-status-danger',
    statusInfo: 'text-status-info',
    statusPending: 'text-status-pending',
    
    // Background status classes
    bgStatusSuccess: 'bg-status-success',
    bgStatusWarning: 'bg-status-warning',
    bgStatusDanger: 'bg-status-danger',
    bgStatusInfo: 'bg-status-info',
    bgStatusPending: 'bg-status-pending',
    
    // Common combinations
    card: 'bg-background-card border border-border-light',
    input: 'bg-background-input border border-border-default focus:border-border-focus',
    button: 'bg-primary-600 hover:bg-primary-700 text-white',
    buttonSecondary: 'bg-secondary-600 hover:bg-secondary-700 text-white',
    
    // Layout
    page: 'bg-background-primary text-text-primary min-h-screen',
    section: 'bg-background-secondary p-6 rounded-lg',
    
    // Typography
    heading: 'text-text-primary font-semibold',
    subheading: 'text-text-secondary',
    paragraph: 'text-text-primary',
    label: 'text-text-secondary font-medium',
  }
} 